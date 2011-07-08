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

namespace JMS\DiExtraBundle\Tests\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\DiExtraBundle\DependencyInjection\JMSDiExtraExtension;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use JMS\DiExtraBundle\DependencyInjection\Compiler\AnnotationConfigurationPass;

class AnnotationConfigurationPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $container = $this->getContainer(array(), array(
            __DIR__.'/../../Fixture/',
        ));
        $container->set('doctrine.entity_manager', $em = new \stdClass);
        $container->set('session', $session = new \stdClass);
        $container->set('database_connection', $dbCon = new \stdClass);
        $container->set('router', $router = new \stdClass);
        $container->setParameter('table_name', 'foo');
        $this->process($container);

        $this->assertTrue($container->hasDefinition('j_m_s.di_extra_bundle.tests.fixture.request_listener'));
        $service = $container->get('j_m_s.di_extra_bundle.tests.fixture.request_listener');
        $this->assertAttributeEquals($em, 'em', $service);
        $this->assertAttributeEquals($session, 'session', $service);
        $this->assertAttributeEquals($dbCon, 'con', $service);
        $this->assertAttributeEquals($router, 'router', $service);
        $this->assertAttributeEquals('foo', 'table', $service);
    }

    public function testProcessValidator()
    {
        $container = $this->getContainer(array(), array(
            __DIR__.'/../../Fixture/Validator',
        ));
        $container->set('foo', $foo = new \stdClass);
        $this->process($container);

        $this->assertTrue($container->hasDefinition('j_m_s.di_extra_bundle.tests.fixture.validator.validator'));

        $def = $container->getDefinition('j_m_s.di_extra_bundle.tests.fixture.validator.validator');
        $this->assertEquals(array(
            'validator.constraint_validator' => array(
                array('alias' => 'foobar'),
            )
        ), $def->getTags());

        $v = $container->get('j_m_s.di_extra_bundle.tests.fixture.validator.validator');
        $this->assertAttributeEquals($foo, 'foo', $v);
    }

    private function getContainer(array $bundles = array(), array $directories = array())
    {
        $container = new ContainerBuilder();
        $container->set('annotation_reader', new AnnotationReader());
        $container->setParameter('kernel.debug', false);

        $extension = new JMSDiExtraExtension();
        $extension->load(array(array(
            'locations' => array(
                'bundles' => $bundles,
                'directories' => $directories,
            ),
            'metadata' => array(
                'cache' => 'none',
            )
        )), $container);

        return $container;
    }

    private function process(ContainerBuilder $container, array $bundles = array())
    {
        $kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
        $kernel
            ->expects($this->once())
            ->method('getBundles')
            ->will($this->returnValue($bundles))
        ;

        $pass = new AnnotationConfigurationPass($kernel);
        $pass->process($container);
    }
}