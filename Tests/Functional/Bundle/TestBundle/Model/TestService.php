<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Model;

use JMS\DiExtraBundle\Annotation\Service;

/**
 * JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Model\TestService
 *
 * @Service("jms_di_extra.model.test_service",
 * factoryService = "jms_di_extra.factory.test_factory",
 * factoryMethod="create", factoryMethodArguments={
 * "message" = "created by factory"
 * } )
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class TestService
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
