<?php

namespace JMS\DiExtraBundle\Tests\Functional;

class Issue219Test extends BaseTestCase
{
    public function testDoctrineRepositoryInjection()
    {
        $kernel = static::createKernel(array('debug' => false, 'config' => 'issue-219.yml'));
        $kernel->boot();

        $manager = $kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $repository = $manager->getRepository('\JMS\DiExtraBundle\Tests\Functional\Entities\TestEntity');

        $this->assertSame($kernel->getContainer()->get('some_service'), $repository->getService());
        $this->assertEquals("foo_42", $repository->getParameter());
    }
}
