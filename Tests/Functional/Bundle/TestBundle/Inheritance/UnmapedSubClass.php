<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Inheritance;

use JMS\DiExtraBundle\Annotation as DI;

class UnmapedSubClass extends MappedSuperClass
{
    public function getFoo()
    {
        return new \ArrayObject([]);
    }
}
