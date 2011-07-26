<?php

namespace JMS\DiExtraBundle\Tests\Finder;

use JMS\DiExtraBundle\Finder\ServiceFinder;

class FindstrServiceFinderTest extends AbstractServiceFinderTest
{
    protected function getFinder()
    {
        if (0 !== stripos(PHP_OS, 'win')) {
            $this->markTestSkipped('FINDSTR is only available on Windows.');
        }
        if (false !== strpos(php_uname(), 'Windows XP')) {
            $this->markTestSkipped('FINDSTR produces unusable output on Windows XP.');
        }
        
        $finder = new ServiceFinder();
        $ref = new \ReflectionProperty($finder, 'method');
        $ref->setAccessible(true);
        $ref->setValue($finder, ServiceFinder::METHOD_FINDSTR);
        
        return $finder;
    }
}