<?php

namespace JMS\DiExtraBundle\Annotation;

use JMS\DiExtraBundle\Exception\InvalidTypeException;

final class Tag
{
    public $name;
    public $attributes = array();

    public function __construct(array $values)
    {
        if (!isset($values['value'])) {
            throw new \InvalidArgumentException('A value must be given for annotation "@Tag".');
        }
        if (!is_string($values['value'])) {
            throw new InvalidTypeException('Tag', 'value', 'string', $values['value']);
        }

        $this->name = $values['value'];

        if (isset($values['attributes'])) {
            if (!is_array($values['attributes'])) {
                throw new InvalidTypeException('Tag', 'attributes', 'array', $values['attributes']);
            }

            $this->attributes = $values['attributes'];
        }
    }
}