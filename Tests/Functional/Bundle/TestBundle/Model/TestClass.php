<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Model;

use JMS\DiExtraBundle\Annotation\Service;

/**
 * JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Model\TestClass
 *
 * @Service("jms_di_extra.model.test",
 * factoryClass = "JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Factory\TestClassFactory",
 * factoryMethod="create", factoryMethodArguments={
 * "message" = "created by factory"
 * } )
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class TestClass 
{
    /**
     * @var string
     */
    protected $testMessage;

    /**
     * @param string $testMessage
     * @return TestClass
     */
    public function setTestMessage($testMessage)
    {
        $this->testMessage = $testMessage;

        return $this;
    }

    /**
     * @return string
     */
    public function getTestMessage()
    {
        return $this->testMessage;
    }


}
