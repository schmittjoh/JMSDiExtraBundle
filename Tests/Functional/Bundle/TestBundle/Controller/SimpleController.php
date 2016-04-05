<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Controller;

use JMS\DiExtraBundle\Annotation as DI;

class SimpleController
{
    protected $name;

    /**
     * @DI\InjectParams({
     *     "name" = @DI\Inject("%foo%")
     * })
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

}
