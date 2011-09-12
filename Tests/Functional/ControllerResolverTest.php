<?php

namespace JMS\DiExtraBundle\Tests\Functional;

class ControllerResolverTest extends BaseTestCase
{
    public function testLookupMethodIsCorrectlyImplemented()
    {
        $client = $this->createClient();
        $client->request('GET', '/register');

        $this->assertEquals('foo@bar.de', $client->getResponse()->getContent());
    }

    public function testLookupMethodAndAopProxy()
    {
        $client = $this->createClient();
        $client->request('GET', '/lookup-method-and-aop');

        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'), substr((string) $client->getResponse(), 0, 512));

        $client->insulate();
        $client->request('GET', '/lookup-method-and-aop');
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'), substr((string) $client->getResponse(), 0, 512));
    }

    public function testAopProxyWhenNoDiMetadata()
    {
        $client = $this->createClient();
        $client->request('GET', '/secure-action');

        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }
}