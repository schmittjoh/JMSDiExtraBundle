<?php

namespace JMS\DiExtraBundle\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\DiExtraBundle\Metadata\DefaultNamingStrategy;
use JMS\DiExtraBundle\Metadata\Driver\AnnotationDriver;

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

    public function testCustomAnnotationOnMethod()
    {
        $metadata = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture\MethodMetaProcessor'));
        $this->assertEquals('fancy', @$metadata->tags['omg'], 'check key and value of custom annotation');
    }


    private function getDriver()
    {
        return new AnnotationDriver(new AnnotationReader(), new DefaultNamingStrategy());
    }
}
