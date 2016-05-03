<?php

namespace JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture;

use JMS\DiExtraBundle\Annotation as DI; // Use this alias in order to not have this class picked up by the finder

/**
 * @DI\Service(
 *     id="test.service",
 *     environments={"dev", "test"},
 *     decorates="test.service",
 *     decoration_inner_name="original.test.service",
 *     deprecated="use new.test.service instead",
 *     public=false,
 *     autowire=false,
 *     autowiringTypes={"JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture\Service"}
 * )
 *
 * @author wodka
 */
class Service
{
}
