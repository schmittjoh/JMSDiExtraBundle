<?php

namespace JMS\DiExtraBundle\Tests\Metadata\Driver\Fixture;

use JMS\DiExtraBundle\Annotation as DI; // Use this alias in order to not have this class picked up by the finder

/**
 * @DI\Service(
 *     id="test.service",
 *     decorates="test.service",
 *     decoration_inner_name="original.test.service",
 *     deprecated="use new.test.service instead",
 *     public=false
 * )
 *
 * @author wodka
 */
class Service
{
}
