<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\DiExtraBundle\HttpKernel;

use Symfony\Component\DependencyInjection\Compiler\ResolveDefinitionTemplatesPass;

use JMS\DiExtraBundle\Generator\DefinitionInjectorGenerator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use JMS\DiExtraBundle\Generator\LookupMethodClassGenerator;
use JMS\DiExtraBundle\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\ConfigCache;
use Metadata\MetadataFactory;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver as BaseControllerResolver;

class ControllerResolver extends BaseControllerResolver
{
    protected function createController($controller)
    {
        if (false === $pos = strpos($controller, '::')) {
            $count = substr_count($controller, ':');
            if (2 == $count) {
                // controller in the a:b:c notation then
                $controller = $this->parser->parse($controller);
                $pos = strpos($controller, '::');
            } elseif (1 == $count) {
                // controller in the service:method notation
                list($service, $method) = explode(':', $controller);

                return array($this->container->get($service), $method);
            } else {
                throw new \LogicException(sprintf('Unable to parse the controller name "%s".', $controller));
            }
        }

        $class = substr($controller, 0, $pos);
        $method = substr($controller, $pos+2);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $filename = $this->container->getParameter('jms_di_extra.cache_dir').'/controller_injectors/'.str_replace('\\', '', $class).'.php';
        $cache = new ConfigCache($filename, $this->container->getParameter('kernel.debug'));

        if (!$cache->isFresh()) {
            if (null === $metadata = $this->container->get('jms_di_extra.metadata.metadata_factory')->getMetadataForClass($class)) {
                $controller = new $class();
                if ($controller instanceof ContainerAwareInterface) {
                    $controller->setContainer($this->container);
                }

                return array($controller, $method);
            }

            $this->prepareContainer($cache, $filename, $metadata);
        }

        $inject = require $filename;

        return array($inject($this->container), $method);
    }

    private function prepareContainer($cache, $containerFilename, $metadata)
    {
        $container = new ContainerBuilder();

        // add resources
        $ref = $metadata->getOutsideClassMetadata()->reflection;
        while ($ref && false !== $filename = $ref->getFilename()) {
            $container->addResource(new FileResource($filename));
            $ref = $ref->getParentClass();
        }

        // add definitions
        $definitions = $this->container->get('jms_di_extra.metadata.converter')->convert($metadata);
        $serviceIds = $parameters = array();

        $controllerDef = array_pop($definitions);
        $container->setDefinition('controller', $controllerDef);

        foreach ($definitions as $id => $def) {
            $container->setDefinition($id, $def);
        }

        $this->generateLookupMethods($controllerDef, $metadata);

        $pass = new ResolveDefinitionTemplatesPass();
        $pass->process($container);

        if (!file_exists($dir = dirname($containerFilename))) {
            if (false === @mkdir($dir, 0777, true)) {
                throw new \RuntimeException(sprintf('Could not create directory "%s".', $dir));
            }
        }

        static $generator;
        if (null === $generator) {
            $generator = new DefinitionInjectorGenerator();
        }

        $cache->write($generator->generate($container->getDefinition('controller')), $container->getResources());
    }

    private function generateLookupMethods($def, $metadata)
    {
        // generate lookup methods where requested
        $lookupMethods = array();
        $outsideClass = $metadata->getOutsideClassMetadata()->reflection;
        foreach ($metadata->classMetadata as $classMetadata) {
            if (!$classMetadata->lookupMethods) {
                continue;
            }

            foreach ($classMetadata->lookupMethods as $name => $value) {
                if ($outsideClass->getMethod($name)->getDeclaringClass()->getName() !== $classMetadata->name) {
                    continue;
                }

                $lookupMethods[$name] = $value;
            }
        }

        if ($lookupMethods) {
            static $generator;
            if (null === $generator) {
                $generator = new LookupMethodClassGenerator();
            }

            $lookupClassName = str_replace('\\', '', $outsideClass->getName());
            $code = $generator->generate($outsideClass, $lookupMethods, $lookupClassName);

            $filename = $this->container->getParameter('jms_di_extra.cache_dir').'/lookup_method_classes/'.$lookupClassName.'.php';
            if (!file_exists($dir = dirname($filename))) {
                if (false === @mkdir($dir, 0777, true)) {
                    throw new \RuntimeException(sprintf('Could not create directory "%s".', $dir));
                }
            }
            file_put_contents($filename, $code);
            require_once $filename;

            $def->setFile($filename);
            $def->setClass('JMS\DiExtraBundle\DependencyInjection\LookupMethodClass\\'.$lookupClassName);
            $def->setProperty('__symfonyDependencyInjectionContainer', new Reference('service_container'));
        }
    }
}