<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Controller\Base;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use JMS\DiExtraBundle\Annotation as DI;

class Controller extends BaseController
{
    /**
     * @DI\Inject("%foo%")
     */
    protected $foo;
}
