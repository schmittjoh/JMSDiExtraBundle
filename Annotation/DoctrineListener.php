<?php

namespace JMS\DiExtraBundle\Annotation;

use JMS\DiExtraBundle\Exception\InvalidTypeException;

/**
 * @Annotation
 * @Target("CLASS")
 */
class DoctrineListener
{
    /** @var array<string> @Required */
    public $events;

    /** @var string */
    public $connection;

    /** @var boolean */
    public $lazy = true;

    /** @var integer */
    public $priority = 0;

    public function __construct()
    {
        if (0 === func_num_args()) {
            return;
        }
        $values = func_get_arg(0);

        if (!isset($values['value'])) {
            throw new InvalidTypeException('DoctrineListener', 'value', 'array or string', null);
        }
        $this->events = (array) $values['value'];

        if (isset($values['connection'])) {
            if (!is_string($values['connection'])) {
                throw new InvalidTypeException('DoctrineListener', 'connection', 'string', $values['connection']);
            }
            $this->connection = $values['connection'];
        }

        if (isset($values['lazy'])) {
            if (!is_boolean($values['lazy'])) {
                throw new InvalidTypeException('DoctrineListener', 'lazy', 'boolean', $values['lazy']);
            }
            $this->lazy = $values['lazy'];
        }

        if (isset($values['priority'])) {
            if (!is_integer($values['priority'])) {
                throw new InvalidTypeException('DoctrineListener', 'priority', 'integer', $values['priority']);
            }
            $this->priority = $values['priority'];
        }
    }
}