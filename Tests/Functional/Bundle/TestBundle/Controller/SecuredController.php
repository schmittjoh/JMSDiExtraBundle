<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Routing\Annotation\Controller;

/**
 * @Controller
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class SecuredController
{
    /**
     * @Route("/lookup-method-and-aop")
     * @Secure("ROLE_FOO")
     */
    public function secureAction()
    {
        throw new \Exception('Should never be called');
    }

    /** @DI\LookupMethod */
    abstract protected function getTestMailer();
}