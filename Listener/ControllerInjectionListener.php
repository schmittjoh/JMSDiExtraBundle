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

namespace JMS\DiExtraBundle\Listener;

use Metadata\MetadataFactory;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ControllerInjectionListener
{
    private $container;
    private $metadataFactory;

    public function __construct(ContainerInterface $container, MetadataFactory $metadataFactory)
    {
        $this->container = $container;
        $this->metadataFactory = $metadataFactory;
    }

    public function onCoreController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        if (null === $metadata = $this->metadataFactory->getMetadataForClass(get_class($controller[0]))) {
            return;
        }

        foreach ($metadata->classMetadata as $cMetadata) {
            foreach ($cMetadata->properties as $name => $value) {
                $property = $cMetadata->reflection->getProperty($name);
                $property->setAccessible(true);

                if ($value instanceof Reference) {
                    $value = $this->container->get((string) $value, $value->getInvalidBehavior());
                } else if ('%' === $value[0]) {
                    $value = $this->container->getParameter(substr($value, 1, -1));
                }
                $property->setValue($controller[0], $value);
            }
        }
    }
}