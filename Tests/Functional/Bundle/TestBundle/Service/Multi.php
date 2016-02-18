<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Inheritance;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("first.multi")
 * @DI\Service("second.multi")
 * @DI\Service("third.multi", environments={"dev"})
 * @DI\Service("fourth.multi", environments={"test"})
 */
class Multi
{
    public $nice;
    public $worker;
    public $first;

    /**
     * @DI\InjectParams(
     *     {
     *         "nice" = @DI\Inject("nice")
     *     }
     * )
     */
    public function init($nice)
    {
        $this->nice = $nice;
    }

    /**
     * @DI\InjectParams(
     *     params = {
     *         "worker" = @DI\Inject("foo")
     *     },
     *     services = {"first.multi"}
     * )
     *
     * @DI\InjectParams(
     *     params = {
     *         "worker" = @DI\Inject("bar")
     *     },
     *     services = {"second.multi"}
     * )
     */
    public function initWorker($worker)
    {
        $this->worker = $worker;
    }

    /**
     * @DI\AfterSetup(services = {"first.multi"})
     */
    public function initFirst()
    {
        $this->first = true;
    }
}
