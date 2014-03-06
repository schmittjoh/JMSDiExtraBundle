<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Traits;

use Symfony\Component\Templating\EngineInterface;
use JMS\DiExtraBundle\Annotation as DI;

trait TemplatableTrait
{
    private $templating;

    /**
     * @DI\InjectParams
     *
     * @param EngineInterface $templating
     */
    public function setTemplating(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function getTemplating()
    {
        return $this->templating;
    }
}
