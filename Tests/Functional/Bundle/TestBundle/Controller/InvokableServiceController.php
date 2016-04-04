<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("controller.invokable")
 */
class InvokableServiceController
{
    public function __invoke()
    {
        return new Response('invoked');
    }
}
