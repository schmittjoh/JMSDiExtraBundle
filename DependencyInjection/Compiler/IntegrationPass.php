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

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Integrates the bundle with external code.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class IntegrationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // replace Symfony2's default controller resolver
        $container->setAlias('controller_resolver', new Alias('jms_di_extra.controller_resolver', false));

        // replace SensioFrameworkExtraBundle's default template listener
        if ($container->hasDefinition('sensio_framework_extra.view.listener')) {
            $def = $container->getDefinition('sensio_framework_extra.view.listener');

            // only overwrite if it has the default class otherwise the user has to do the integration manually
            if ('%sensio_framework_extra.view.listener.class%' === $def->getClass()) {
                $def->setClass('%jms_di_extra.template_listener.class%');
            }
        }

        if ($container->hasDefinition('sensio_framework_extra.controller.listener')) {
            $def = $container->getDefinition('sensio_framework_extra.controller.listener');

            if ('%sensio_framework_extra.controller.listener.class%' === $def->getClass()) {
                $def->setClass('%jms_di_extra.controller_listener.class%');
            }
        }
    }
}