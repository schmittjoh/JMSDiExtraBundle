<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Mailer;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("test_mailer")
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class TestMailer
{
    public function getFromMail()
    {
        return 'foo@bar.de';
    }
}