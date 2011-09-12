<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Secured Controller.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class AnotherSecuredController
{
    /**
     * @Route("/secure-action")
     * @Secure("ROLE_FOO")
     */
    public function secureAction()
    {
        throw new \Exception('Should never be called');
    }
}