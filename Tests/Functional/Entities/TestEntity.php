<?php

namespace JMS\DiExtraBundle\Tests\Functional\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\JMS\DiExtraBundle\Tests\Functional\Entities\TestEntityRepository")
 */
class TestEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;
}
