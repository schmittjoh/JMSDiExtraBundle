<?php

namespace JMS\DiExtraBundle\Tests\Functional;

class MultiServiceInjectTest extends BaseTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testConstructorInjectionWithInheritance()
    {
        $this->createClient();

        $container = self::$kernel->getContainer();
        $nice = $container->get('nice');
        $foo = $container->get('foo');
        $bar = $container->get('bar');
        $first = $container->get('first.multi');
        $second = $container->get('second.multi');

        $this->assertSame($nice, $first->nice, 'all injections without restriction should be done');
        $this->assertSame($foo, $first->worker);
        $this->assertSame(true, $first->first);

        $this->assertSame($nice, $second->nice, 'all injections without restriction should be done');
        $this->assertSame($bar, $second->worker);
        $this->assertSame(null, $second->first);

        $this->assertEquals(false, $container->has('third.multi'), 'should load third service only in dev');
    }

    /**
     * @runInSeparateProcess
     */
    public function testEnvironmentLoading()
    {
        // this is broken -> cannot load any other environment than test
        $this->createClient(
            array(
                'environment' => 'dev'
            )
        );
        $container = self::$kernel->getContainer();

        $this->assertEquals(true, $container->has('fourth.multi'), 'should load fourth service only in test');
    }
}
