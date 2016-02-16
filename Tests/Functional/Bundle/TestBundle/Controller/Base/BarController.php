<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Controller\Base;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BarController extends Controller
{
    /**
     * @DI\Inject("%bar%")
     */
    protected $bar;

    /**
     * @Route("/base/bar")
     *
     * @return Response
     */
    public function barAction()
    {
        return new Response($this->bar);
    }
}
