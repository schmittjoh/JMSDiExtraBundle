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
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

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

        if (true === $container->getParameter('jms_di_extra.doctrine_integration')) {
            $this->integrateWithDoctrine($container);
        }
    }

    private function integrateWithDoctrine(ContainerBuilder $container)
    {
        $entityManagerNames = array_keys($container->getParameter('doctrine.entity_managers'));

        foreach ($entityManagerNames as $emName) {
            // See: https://github.com/doctrine/DoctrineBundle/blob/c9f8cc06153a70433d2c67393f10725959f7bb43/DependencyInjection/DoctrineExtension.php#L384-L385
            $ormConfigDef = $container->getDefinition(sprintf('doctrine.orm.%s_configuration', $emName));

            $originalRepositoryFactoryRef = null;

            foreach ($ormConfigDef->getMethodCalls() as $methodCall) {
                list($methodName, $arguments) = $methodCall;

                if ('setRepositoryFactory' === $methodName) {
                    $originalRepositoryFactoryRef = $arguments[0];

                    $ormConfigDef->removeMethodCall($methodName);

                    break;
                }
            }

            $replacedRepositoryFactoryId = sprintf('jms_di_extra.doctrine.orm.%s.repository_factory', $emName);

            $container->register($replacedRepositoryFactoryId, 'JMS\DiExtraBundle\Doctrine\ORM\ContainerAwareRepositoryFactoryDecorator')
                ->setPublic(false)
                ->setArguments(array(
                    new Reference('service_container'),
                    $originalRepositoryFactoryRef,
                ))
            ;

            $ormConfigDef->addMethodCall('setRepositoryFactory', array(new Reference($replacedRepositoryFactoryId)));
        }
    }
}
