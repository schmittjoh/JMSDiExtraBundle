<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Factory;

use JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Model\TestClass;
use JMS\DiExtraBundle\Annotation\Service;

/**
 * JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Factory\TestClassFactory
 *
 * @Service("jms_di_extra.factory.test_factory")
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class TestClassFactory 
{
    /**
     * @param string $message
     * @return TestClass
     */
    public function create($message = '')
    {
        $class = new TestClass();
        $class->setTestMessage($message);

        return $class;
    }
}
