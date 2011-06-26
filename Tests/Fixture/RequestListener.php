<?php

namespace JMS\DiExtraBundle\Tests\Fixture;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class RequestListener
{
    private $router;
    private $session;
    private $em;
    private $con;
    private $table;

    /**
     * @DI\Inject
     */
    private $kernel;

    /**
     * @DI\InjectParams({
     *     "em" = @DI\Inject("doctrine.entity_manager")
     * })
     */
    public function __construct($router, $session, $em)
    {
        $this->router = $router;
        $this->session = $session;
        $this->em = $em;
    }

    /**
     * @DI\InjectParams({
     *     "table" = @DI\Inject("%table_name%")
     * })
     */
    public function setConnection($databaseConnection, $table)
    {
        $this->con   = $databaseConnection;
        $this->table = $table;
    }
}