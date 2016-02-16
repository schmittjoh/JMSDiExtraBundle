<?php

namespace JMS\DiExtraBundle\Tests\DependencyInjection\Collection;

use JMS\DiExtraBundle\DependencyInjection\Collection\LazyServiceMap;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LazyServiceMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LazyServiceMap
     */
    private $map;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|ContainerInterface
     */
    private $container;

    public function testGet()
    {
        $this->container->expects($this->once())
            ->method('get')
            ->with('bar_service')
            ->will($this->returnValue($a = new \stdClass));

        $this->assertSame($a, $this->map->get('foo')->get());
        $this->assertSame($a, $this->map->get('foo')->get());
    }

    public function testRemove()
    {
        $this->container->expects($this->once())
            ->method('get')
            ->with('bar_service')
            ->will($this->returnValue($a = new \stdClass));

        $this->assertSame($a, $this->map->remove('foo'));
        $this->assertFalse($this->map->contains($a));
        $this->assertFalse($this->map->containsKey('foo'));
    }

    public function testIterator()
    {
        $this->container->expects($this->at(0))
            ->method('get')
            ->with('bar_service')
            ->will($this->returnValue($a = new \stdClass));

        $this->container->expects($this->at(1))
            ->method('get')
            ->with('baz_service')
            ->will($this->returnValue($b = new \stdClass));

        $iterator = $this->map->getIterator();

        $this->assertSame($a, $iterator->current());

        $iterator->next();
        $this->assertSame($b, $iterator->current());
    }

    protected function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->map = new LazyServiceMap($this->container, array(
            'foo' => 'bar_service',
            'bar' => 'baz_service',
        ));
    }
}
