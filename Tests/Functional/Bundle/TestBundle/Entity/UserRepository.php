<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Entity;

use Doctrine\ORM\EntityRepository;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Not declaring as Service
 *
 * @author Aleksandr Moroz <netstroix@gmail.com>
 */
class UserRepository extends EntityRepository
{
    protected $param;
    protected $em;

    /**
     * @DI\InjectParams({
     *     "param" = @DI\Inject("%some.parameter%"),
     *     "em" = @DI\Inject("doctrine.orm.default_entity_manager")
     * })
     */
    public function setParam($param, $em)
    {
        $this->param = $param;
        $this->em = $em;
    }
}
