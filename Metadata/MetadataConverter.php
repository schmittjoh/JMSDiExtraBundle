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

use JMS\DiExtraBundle\Exception\InvalidParentException;
use JMS\DiExtraBundle\Exception\InvalidAnnotationException;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Definition;
use Metadata\ClassHierarchyMetadata;

class MetadataConverter
{
    /**
     * Converts class hierarchy metadata to definition instances.
     *
     * @param ClassHierarchyMetadata $metadata
     * @param string                 $environment
     * @return array an array of Definition instances
     */
    public function convert(ClassHierarchyMetadata $metadata, $environment = null)
    {
        static $count = 0;
        $definitions = array();

        /** @var ClassMetadata $previous */
        $previous = null;
        /** @var ClassMetadata $classMetadata */
        foreach ($metadata->classMetadata as $classMetadata) {
            foreach ($classMetadata->getServices() as $service) {
                if (isset($environment)
                    && isset($service['environments'])
                    && sizeof($service['environments']) > 0
                    && !in_array($environment, $service['environments'])
                ) {
                    continue;
                }

                if (null === $previous && !isset($service['parent'])) {
                    $definition = new Definition();
                } else {
                    if (!isset($service['parent']) && sizeof($previous->getServices()) > 1) {
                        throw new InvalidParentException('there are multiple services on '.$classMetadata->name);
                    }

                    $definition = new DefinitionDecorator(
                        @$service['parent'] ?: $previous->id
                    );
                }

                if (!isset($service['id'])) {
                    $service['id'] = '_jms_di_extra.unnamed.service_' . $count++;
                }

                $definition->setClass($classMetadata->name);
                if (isset($service['scope'])) {
                    if (!method_exists($definition, 'setScope')) {
                        throw new \RuntimeException('service scopes are not available on your Symfony version.');
                    }
                    $definition->setScope($service['scope']);
                }
                if (isset($service['public'])) {
                    $definition->setPublic($service['public']);
                }
                if (isset($service['abstract'])) {
                    $definition->setAbstract($service['abstract']);
                }
                if (null !== $classMetadata->arguments) {
                    $definition->setArguments($classMetadata->arguments);
                }

                $definition->setMethodCalls($this->reduceMethodCalls($classMetadata->methodCalls, $service['id']));
                $definition->setTags($classMetadata->tags);
                $definition->setProperties($classMetadata->properties);

                if (isset($service['decorates'])) {
                    if (!method_exists($definition, 'setDecoratedService')) {
                        throw new InvalidAnnotationException(
                            sprintf(
                                "decorations require symfony >=2.8 on class %s",
                                $classMetadata->name
                            )
                        );
                    }

                    $definition->setDecoratedService($service['decorates'], $service['decoration_inner_name']);
                }

                if (isset($service['deprecated']) && method_exists($definition, 'setDeprecated')) {
                    $definition->setDeprecated(true, $service['deprecated']);
                }

            if (0 !== count($classMetadata->initMethods)) {
                foreach ($this->reduceMethodCalls($classMetadata->initMethods, $service['id']) as $initMethod) {
                    $definition->addMethodCall($initMethod[0]);
                }
            } elseif (null !== $classMetadata->initMethod) {
                @trigger_error('ClassMetadata::$initMethod is deprecated since version 1.7 and will be removed in 2.0. Use ClassMetadata::$initMethods instead.', E_USER_DEPRECATED);
                $definition->addMethodCall($classMetadata->initMethod);
            }

                $definitions[$service['id']] = $definition;
            }

            $previous = $classMetadata;
        }

        return $definitions;
    }

    /**
     * @param $methods
     * @param $serviceId
     *
     * @return array
     */
    private function reduceMethodCalls($methods, $serviceId)
    {
        $reduced = array();

        /*
         * $settings is an array with 3 keys:
         *   0: method name
         *   1: parameters
         *   2: service restriction
         */
        foreach ($methods as $settings) {
            if (isset($settings[2]) && !in_array($serviceId, $settings[2])) {
                continue;
            }

            $reduced[] = $settings;
        }

        return $reduced;
    }
}
