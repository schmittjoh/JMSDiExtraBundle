<?php

namespace JMS\DiExtraBundle\Tests\Functional;


class InjectParamsRepositoryTest extends BaseTestCase
{
    public function testInjectParam()
    {
        $kernel = static::createKernel(array('debug' => true, 'config' => 'doctrine.yml'));
        $kernel->boot();


        $em = $kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $repo = $em->getRepository('JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Entity\User');

        $this->assertAttributeEquals($em, 'em', $repo);
        $this->assertAttributeEquals($kernel->getContainer()->getParameter('some.parameter'), 'param', $repo);
    }
}