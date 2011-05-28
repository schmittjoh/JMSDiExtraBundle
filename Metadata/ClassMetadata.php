<?php

namespace JMS\DiExtraBundle\Metadata;

use Metadata\ClassMetadata as BaseClassMetadata;

class ClassMetadata extends BaseClassMetadata
{
    public $id;
    public $parent;
    public $scope;
    public $public;
    public $abstract;
    public $tags = array();
    public $properties = array();

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->parent,
            $this->scope,
            $this->public,
            $this->abstract,
            $this->tags,
            $this->properties,
            parent::serialize(),
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->id,
            $this->parent,
            $this->scope,
            $this->public,
            $this->abstract,
            $this->tags,
            $this->properties,
            $parentStr
        ) = unserialize($str);

        parent::unserialize($parentStr);
    }
}