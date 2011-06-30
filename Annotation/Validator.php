<?php

namespace JMS\DiExtraBundle\Annotation;

use JMS\DiExtraBundle\Exception\InvalidTypeException;

/** @Annotation */
final class Validator
{
    public $alias;

    public function __construct(array $values)
    {
        if (isset($values['alias'])) {
            $values['value'] = $values['alias'];
        }

        if (!isset($values['value'])) {
            throw new \InvalidArgumentException('A value must be given for @Validator annotations.');
        }
        if (!is_string($values['value'])) {
            throw new InvalidTypeException('Validator', 'value', 'string', $values['value']);
        }
        $this->alias = $values['value'];
    }
}