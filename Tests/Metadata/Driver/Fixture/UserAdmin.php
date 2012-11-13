<?php

namespace JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture;

use Sonata\AdminBundle\Admin\Admin;
use JMS\DiExtraBundle as DI;

/**
 * @DI\Annotation\Service
 * @DI\Annotation\Tag("sonata.admin", attributes={"manager_type"="orm", "group"="User", "label"="User"})
 */
class UserAdmin extends Admin
{
    /**
     * @DI\Annotation\InjectParams({
     *     "code"=@DI\Annotation\InjectValue(null),
     *     "class"=@DI\Annotation\InjectValue("Acme\TestBundle\Entity\User"),
     *     "baseControllerName"=@DI\Annotation\InjectValue("SonataAdminBundle:CRUD")
     * })
     */
    public function __construct($code, $class, $baseControllerName)
    {
    }
}
