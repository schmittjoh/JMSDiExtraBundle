<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Controller\Base;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FooController extends Controller
{
    /**
     * @Route("/base/foo")
     *
     * @return Response
     */
    public function indexAction()
    {
        return new Response($this->foo);
    }
}
