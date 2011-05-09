<?php

namespace JMS\DiExtraBundle\Annotation;

use JMS\DiExtraBundle\Exception\InvalidTypeException;

class Service
{
    public $id;
    public $parent;
    public $public;
    public $scope;
    public $abstract;

    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            if (!is_string($values['value'])) {
                throw new InvalidTypeException('Service', 'value', 'string', $values['value']);
            }

            $this->id = $values['value'];
        }
        if (isset($values['parent'])) {
            if (!is_string($values['parent'])) {
                throw new InvalidTypeException('Service', 'parent', 'string', $values['parent']);
            }

            $this->parent = $values['parent'];
        }
        if (isset($values['public'])) {
            if (!is_bool($values['public'])) {
                throw new InvalidTypeException('Service', 'public', 'boolean', $values['public']);
            }

            $this->public = $values['public'];
        }
        if (isset($values['scope'])) {
            if (!is_string($values['scope'])) {
                throw new InvalidTypeException('Service', 'scope', 'string', $values['scope']);
            }

            $this->scope = $values['scope'];
        }
        if (isset($values['abstract'])) {
            if (!is_bool($values['abstract'])) {
                throw new InvalidTypeException('Service', 'abstract', 'boolean', $values['abstract']);
            }

            $this->abstract = $values['abstract'];
        }
    }
}