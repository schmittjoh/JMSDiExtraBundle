<?php

namespace JMS\DiExtraBundle\Tests\Functional;

class Issue195Test extends BaseTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testInheritance()
    {
        // /inheritance/extended

        $client = $this->createClient();
        $client->request('GET', '/inheritance/extended');

        $this->assertEquals('hello', $client->getResponse()->getContent(), 'constructor injection of implementation should be used');

    }
}
