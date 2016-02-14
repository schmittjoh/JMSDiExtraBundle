<?php

namespace JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture;

use JMS\DiExtraBundle as DI; // Use this alias in order to not have this class picked up by the finder

/**
 * @DI\Annotation\Service("first.service")
 * @DI\Annotation\Service("second.service")
 */
class MultiService
{

    /**
     * @DI\Annotation\InjectParams(
     *     params = {
     *         "worker" = @DI\Annotation\Inject("foo")
     *     },
     *     services = {"first.service"}
     * )
     *
     * @DI\Annotation\InjectParams(
     *     params = {
     *         "worker" = @DI\Annotation\Inject("bar")
     *     },
     *     services = {"second.service"}
     * )
     */
    public function init($worker)
    {}
}
