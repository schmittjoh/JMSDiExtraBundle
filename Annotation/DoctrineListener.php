<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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