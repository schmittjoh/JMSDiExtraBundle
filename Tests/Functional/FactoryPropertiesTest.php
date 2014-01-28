<?php

namespace JMS\DiExtraBundle\Tests\Functional;

/**
 * JMS\DiExtraBundle\Tests\Functional\Bundle\FactoryPropertiesTest
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class FactoryPropertiesTest extends BaseTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function  testFactoryClassProperty()
    {
        $this->createClient();
        $container = self::$kernel->getContainer();
        $testClass = $container->get('jms_di_extra.model.test');
        $this->assertEquals($testClass->getTestMessage(), 'created by factory');
    }

    /**
     * @runInSeparateProcess
     */
    public function  testFactoryServiceProperty()
    {
        $this->createClient();
        $container = self::$kernel->getContainer();
        $testClass = $container->get('jms_di_extra.model.test_service');
        $this->assertEquals($testClass->getTestMessage(), 'created by factory');
    }
}
