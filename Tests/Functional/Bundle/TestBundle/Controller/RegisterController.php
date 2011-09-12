<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Controller;

/**
 * @Controller
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class RegisterController
{
    /**
     * @Route("/register")
     */
    public function registerAction()
    {
        $mailer = $this->getMailer();

        return new Response($mailer->getFromMail(), 200, array('Content-Type' => 'text/plain'));
    }

    /** @DI\LookupMethod("test_mailer") */
    abstract protected function getMailer();
}