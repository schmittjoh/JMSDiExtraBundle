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

namespace JMS\DiExtraBundle\Metadata;

use JMS\DiExtraBundle\Exception\InvalidAnnotationException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Definition;
use Metadata\ClassHierarchyMetadata;

class MetadataConverter
{
    /**
     * Converts class hierarchy metadata to definition instances.
     *
     * @param ClassHierarchyMetadata $metadata
     * @return array an array of Definition instances
     */
    public function convert(ClassHierarchyMetadata $metadata)
    {
        static $count = 0;
        $definitions = array();

        $previous = null;
        foreach ($metadata->classMetadata as $classMetadata) {
            if (null === $previous && null === $classMetadata->parent) {
                $definition = new Definition();
            } else {
                $definition = new DefinitionDecorator(
                    $classMetadata->parent ?: $previous->id
                );
            }

            $definition->setClass($classMetadata->name);
            if (null !== $classMetadata->scope) {
                $definition->setScope($classMetadata->scope);
            }
            if (null !== $classMetadata->shared) {
                $definition->setShared($classMetadata->shared);
            }
            if (null !== $classMetadata->public) {
                $definition->setPublic($classMetadata->public);
            }
            if (null !== $classMetadata->abstract) {
                $definition->setAbstract($classMetadata->abstract);
            }
            if (null !== $classMetadata->arguments) {
                $definition->setArguments($classMetadata->arguments);
            }
            if (null !== $classMetadata->autowire) {
                if (!method_exists($definition, 'setAutowired')) {
                    throw new InvalidAnnotationException(sprintf('You must use symfony 2.8 or higher to use autowiring on the class %s.', $classMetadata->name));
                }

                $definition->setAutowired($classMetadata->autowire);
            }
            if (null !== $classMetadata->autowiringTypes && method_exists($definition, 'setAutowiringTypes')) {
                $definition->setAutowiringTypes($classMetadata->autowiringTypes);
            }

            
            $definition->setMethodCalls($classMetadata->methodCalls);
            $definition->setTags($classMetadata->tags);
            $definition->setProperties($classMetadata->properties);

            if (null !== $classMetadata->decorates) {
                if (!method_exists($definition, 'setDecoratedService')) {
                    throw new InvalidAnnotationException(sprintf('You must use symfony 2.8 or higher to use decorations on the class %s.', $classMetadata->name));
                }

                $definition->setDecoratedService($classMetadata->decorates, $classMetadata->decoration_inner_name);
            }

            if (null !== $classMetadata->deprecated && method_exists($definition, 'setDeprecated')) {
                $definition->setDeprecated(true, $classMetadata->deprecated);
            }

            if (null === $classMetadata->id) {
                $classMetadata->id = '_jms_di_extra.unnamed.service_'.$count++;
            }

            if (0 !== count($classMetadata->initMethods)) {
                foreach ($classMetadata->initMethods as $initMethod) {
                    $definition->addMethodCall($initMethod);
                }
            } elseif (null !== $classMetadata->initMethod) {
                @trigger_error('ClassMetadata::$initMethod is deprecated since version 1.7 and will be removed in 2.0. Use ClassMetadata::$initMethods instead.', E_USER_DEPRECATED);
                $definition->addMethodCall($classMetadata->initMethod);
            }

            $definitions[$classMetadata->id] = $definition;
            $previous = $classMetadata;
        }

        return $definitions;
    }
}
