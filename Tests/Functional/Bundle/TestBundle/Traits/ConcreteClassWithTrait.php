<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Traits;

use Symfony\Component\Templating\EngineInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("concrete_class_with_trait")
 */
class ConcreteClassWithTrait
{
    use TemplatableTrait;
}
