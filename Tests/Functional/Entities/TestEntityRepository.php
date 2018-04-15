<?php

namespace JMS\DiExtraBundle\Tests\Functional\Entities;

use Doctrine\ORM\EntityRepository;
use JMS\DiExtraBundle\Annotation as DI;

class TestEntityRepository extends EntityRepository
{
    private $service;

    private $parameter;

    public function getService()
    {
        return $this->service;
    }

    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * @DI\InjectParams({
     *     "service" = @DI\Inject("some_service")
     * })
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @DI\InjectParams({
     *     "parameter" = @DI\Inject("foo_%some_parameter%")
     * })
     */
    public function setParameter($parameter)
    {
        $this->parameter = $parameter;
    }
}
