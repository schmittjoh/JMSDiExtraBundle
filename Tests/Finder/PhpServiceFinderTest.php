<?php

namespace JMS\DiExtraBundle\Tests\Finder;

use JMS\DiExtraBundle\Finder\ServiceFinder;

class PhpServiceFinderTest extends AbstractServiceFinderTest
{
    protected function getFinder()
    {
        $finder = new ServiceFinder();
        $ref = new \ReflectionProperty($finder, 'method');
        $ref->setAccessible(true);
        $ref->setValue($finder, ServiceFinder::METHOD_FINDER);
        
        return $finder;
    }
}