<?php

namespace JMS\DiExtraBundle\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\DiExtraBundle\Metadata\DefaultNamingStrategy;
use JMS\DiExtraBundle\Metadata\Driver\AnnotationDriver;
use Symfony\Component\DependencyInjection\Reference;

class AnnotationDriverTest extends \PHPUnit_Framework_TestCase
{
    public function testFormType()
    {
        $metadata = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture\LoginType'));

        $this->assertEquals('j_m_s.di_extra_bundle.tests.metadata.driver.fixture.login_type', $metadata->id);
        $this->assertEquals(array(
            'form.type' => array(
                array('alias' => 'login'),
            )
        ), $metadata->tags);
    }

    public function testFormTypeWithExplicitAlias()
    {
        $metadata = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture\SignUpType'));

        $this->assertEquals(array(
            'form.type' => array(
                array('alias' => 'foo'),
            )
        ), $metadata->tags);
    }

    public function testCustomAnnotationOnClass()
    {
        $metadata = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture\ClassMetaProcessor'));
        $this->assertEquals('works', @$metadata->tags['custom'], 'check value of custom annotation');
    }

    public function testServiceAnnotations()
    {
        $metadata = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture\Service'));

        $services = $metadata->getServices();
        $service = array_filter(@$services['test.service'], function ($value) { return $value !== null; });

        $this->assertEquals('test.service', $metadata->id);
        $this->assertArrayHasKey('test.service', $metadata->getServices());
        $this->assertEquals(array(
            'id' => 'test.service',
            'public' => false,
            'decorates' => 'test.service',
            'decoration_inner_name' => 'original.test.service',
            'deprecated' => 'use new.test.service instead',
            'environments' => array()
        ), $service);
        $this->assertEquals(false, $metadata->public);
    }

    public function testCustomAnnotationOnMethod()
    {
        $metadata = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture\MethodMetaProcessor'));
        $this->assertEquals('fancy', @$metadata->tags['omg'], 'check key and value of custom annotation');
    }

    public function testMultiService()
    {
        $metadata = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture\MultiService'));

        $services = $metadata->getServices();
        // this is important to cleanup empty values
        array_walk($services, function (&$service) {
            $service = array_filter($service);
        });

        $this->assertEquals(array(
            'first.service' => array(
                'id' => 'first.service',
            ),
            'second.service' => array(
                'id' => 'second.service',
            )
        ), $services, 'create multiple services');


        $this->assertEquals(
            array(
                array(
                    'init',
                    array(
                        0 => new Reference('foo')
                    ),
                    array('first.service')
                ),
                array(
                    'init',
                    array(
                        0 => new Reference('bar')
                    ),
                    array('second.service')
                )
            ),
            $metadata->methodCalls,
            'service limitation for inject params was used'
        );
    }

    private function getDriver()
    {
        return new AnnotationDriver(new AnnotationReader(), new DefaultNamingStrategy());
    }
}
