<?php

namespace JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\Controller;

use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ExtendedService extends SimpleService
{
    protected $even;
    protected $more;

    /**
     * @DI\InjectParams({
     *     "name" = @DI\Inject("%name%"),
     *     "even" = @DI\Inject("%even%"),
     *     "more" = @DI\Inject("%more%")
     * })
     */
    public function __construct($name, $even, $more)
    {
        parent::__construct($name);

        $this->even = $even;
        $this->more = $more;
    }


    /**
     * @Route("/inheritance/extended")
     */
    public function testAction()
    {
        return new Response(
            $this->name.':'.$this->even.':'.$this->more
        );
    }
}
