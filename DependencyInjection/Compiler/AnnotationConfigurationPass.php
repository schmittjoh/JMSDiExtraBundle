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

namespace JMS\DiExtraBundle\DependencyInjection\Compiler;

use JMS\DiExtraBundle\Exception\RuntimeException;
use JMS\DiExtraBundle\Config\ServiceFilesResource;
use Symfony\Component\Config\Resource\FileResource;
use JMS\DiExtraBundle\Finder\PatternFinder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class AnnotationConfigurationPass implements CompilerPassInterface
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function process(ContainerBuilder $container)
    {
        $factory = $container->get('jms_di_extra.metadata.metadata_factory');
        $converter = $container->get('jms_di_extra.metadata.converter');
        $annotationNamespaces = $container->getParameter('jms_di_extra.annotation_namespaces');
        $annotationNamespaces[] = 'JMS\DiExtraBundle\Annotation';
        $disableGrep = $container->getParameter('jms_di_extra.disable_grep');

        $directories = $this->getScanDirectories($container);
        if (!$directories) {
            $container->getCompiler()->addLogMessage('No directories configured for AnnotationConfigurationPass.');
            return;
        }

        $files = $this->findFiles($directories, $annotationNamespaces, $disableGrep);
        $container->addResource(new ServiceFilesResource($files, $directories, $annotationNamespaces, $disableGrep));
        foreach ($files as $file) {
            $container->addResource(new FileResource($file));
            require_once $file;

            $className = $this->getClassName($file);

            if (null === $metadata = $factory->getMetadataForClass($className)) {
                continue;
            }
            if (null === $metadata->getOutsideClassMetadata()->id) {
                continue;
            }
            if ( ! $metadata->getOutsideClassMetadata()->isLoadedInEnvironment($container->getParameter('kernel.environment'))) {
                continue;
            }

            foreach ($converter->convert($metadata) as $id => $definition) {
                $container->setDefinition($id, $definition);
            }
        }
    }

    private function findFiles(array $directories, array $annotationNamespaces, $disableGrep)
    {
        $files = [];
        foreach ($annotationNamespaces as $namespace) {
            $finder = new PatternFinder($namespace, '*.php', $disableGrep);
            foreach ($finder->findFiles($directories) as $file) {
                $files[$file] = $file;
            }
        }
        return array_values($files);
    }

    private function getScanDirectories(ContainerBuilder $c)
    {
        $bundles = $this->kernel->getBundles();
        $scanBundles = $c->getParameter('jms_di_extra.bundles');
        $scanAllBundles = $c->getParameter('jms_di_extra.all_bundles');

        $directories = $c->getParameter('jms_di_extra.directories');
        foreach ($bundles as $name => $bundle) {
            if (!$scanAllBundles && !in_array($name, $scanBundles, true)) {
                continue;
            }

            if ('JMSDiExtraBundle' === $name) {
                continue;
            }

            $directories[] = $bundle->getPath();
        }

        return $directories;
    }

    /**
     * Only supports one namespaced class per file
     *
     * @throws \RuntimeException if the class name cannot be extracted
     * @param string $filename
     * @return string the fully qualified class name
     */
    private function getClassName($filename)
    {
        $src = file_get_contents($filename);

        if (!preg_match('/\bnamespace\s+([^;]+);/s', $src, $match)) {
            throw new RuntimeException(sprintf('Namespace could not be determined for file "%s".', $filename));
        }
        $namespace = $match[1];

        if (!preg_match('/\b(?:class|trait)\s+([^\s]+)\s+(?:extends|implements|{)/is', $src, $match)) {
            throw new RuntimeException(sprintf('Could not extract class name from file "%s".', $filename));
        }

        return $namespace.'\\'.$match[1];
    }
}
