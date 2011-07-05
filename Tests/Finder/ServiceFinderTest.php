<?php

namespace JMS\DiExtraBundle\Tests\Finder;

use JMS\DiExtraBundle\Finder\ServiceFinder;
use Symfony\Component\Process\ExecutableFinder;

class ServiceFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testFindFilesUsingGrepReturnsEmptyArrayWhenNoMatchesAreFound()
    {
        $executableFinder = new ExecutableFinder();
        if (null === $executableFinder->find('grep')) {
            $this->markTestSkipped('grep is not available.');
        }

        $finder = new ServiceFinder();
        $ref = new  \ReflectionMethod($finder, 'findUsingGrep');
        $ref->setAccessible(true);
        $this->assertEquals(array(), $ref->invoke($finder, array(__DIR__.'/../Fixture/EmptyDirectory')));
    }
}