<?php

namespace JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture;

use Sonata\AdminBundle\Admin\Admin;
use JMS\DiExtraBundle as DI;

/**
 * @DI\Annotation\Admin("Acme\TestBundle\Entity\User", translationDomain="messages")
 */
class UserAdmin extends Admin
{
}
