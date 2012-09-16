<?php

namespace JMS\DiExtraBundle\Tests\Functional;

class Issue48Test extends BaseTestCase
{
    public function testCreatingMultipleKernelsInATest()
    {
        $this->client = static::createClient(array('debug' => false, 'config' => 'doctrine.yml'));
        $kernel = static::createKernel(array('config' => 'doctrine.yml'));
        $kernel->boot();
    }
}