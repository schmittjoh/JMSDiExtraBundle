<?php

namespace JMS\DiExtraBundle\Annotation;

use JMS\DiExtraBundle\Exception\InvalidTypeException;

final class Observe
{
    public $event;
    public $priority = 0;

    public function __construct(array $values)
    {
        if (isset($values['event'])) {
            $values['value'] = $values['event'];
        }

        if (isset($values['value'])) {
            if (!is_string($values['value'])) {
                throw new InvalidTypeException('Observe', 'value', 'string', $values['value']);
            }

            $this->event = $values['value'];
        }

        if (isset($values['priority'])) {
            if (!is_numeric($values['priority'])) {
                throw new InvalidTypeException('Observe', 'priority', 'integer', $values['priority']);
            }

            $this->priority = $values['priority'];
        }
    }
}