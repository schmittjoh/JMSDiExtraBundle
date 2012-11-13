<?php

namespace JMS\DiExtraBundle\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
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

    public function testInjectValue()
    {
        $metadata = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture\UserAdmin'));

        $this->assertEquals('j_m_s.di_extra_bundle.tests.metadata.driver.fixture.user_admin', $metadata->id);
        $this->assertEquals(array(
            'sonata.admin' => array(
                array('manager_type' => 'orm', 'group' => 'User', 'label' => 'User'),
            )
        ), $metadata->tags);
        $this->assertEquals(array(
            null,
            'Acme\TestBundle\Entity\User',
            'SonataAdminBundle:CRUD',
        ), $metadata->arguments);
    }

    private function getDriver()
    {
        return new AnnotationDriver(new AnnotationReader());
    }
}
