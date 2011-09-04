<?php

namespace JMS\DiExtraBundle\Tests\Finder;

use JMS\DiExtraBundle\Finder\ServiceFinder;

class GrepServiceFinderTest extends AbstractServiceFinderTest
{
    protected function getFinder()
    {
        $finder = new ServiceFinder();

        $ref = new \ReflectionProperty($finder, 'grepPath');
        $ref->setAccessible(true);
        if (null === $v = $ref->getValue($finder)) {
            $this->markTestSkipped('grep is not available on your system.');
        }

        return $finder;
    }
}