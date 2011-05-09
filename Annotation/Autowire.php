<?php

namespace JMS\DiExtraBundle\Annotation;

use JMS\DiExtraBundle\Exception\InvalidTypeException;

class Autowire
{
    public $value;
    public $required;

    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            if (!is_string($values['value'])) {
                throw new InvalidTypeException('Autowire', 'value', 'string', $values['value']);
            }

            $this->value = $values['value'];
        }

        if (isset($values['required'])) {
            if (!is_bool($values['required'])) {
                throw new InvalidTypeException('Autowire', 'required', 'boolean', $values['required']);
            }

            $this->required = $values['required'];
        }
    }
}