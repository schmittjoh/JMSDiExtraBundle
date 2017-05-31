<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Inheritance;

use JMS\DiExtraBundle\Annotation as DI;

class MappedSuperClass
{
    private $foo;
    private $bar;

    /**
     * @DI\InjectParams
     *
     * @param stdClass $foo
     * @param stdClass $bar
     */
    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }
}
