<?php

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
        $container->set('kernel', $kernel = new \stdClass);
        $container->set('doctrine.entity_manager', $em = new \stdClass);
        $container->set('session', $session = new \stdClass);
        $container->set('database_connection', $dbCon = new \stdClass);
        $container->set('router', $router = new \stdClass);
        $container->setParameter('table_name', 'foo');
        $this->process($container);

        $this->assertTrue($container->hasDefinition('j_m_s.di_extra_bundle.tests.fixture.request_listener'));
        $service = $container->get('j_m_s.di_extra_bundle.tests.fixture.request_listener');
        $this->assertAttributeEquals($kernel, 'kernel', $service);
        $this->assertAttributeEquals($em, 'em', $service);
        $this->assertAttributeEquals($session, 'session', $service);
        $this->assertAttributeEquals($dbCon, 'con', $service);
        $this->assertAttributeEquals($router, 'router', $service);
        $this->assertAttributeEquals('foo', 'table', $service);
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