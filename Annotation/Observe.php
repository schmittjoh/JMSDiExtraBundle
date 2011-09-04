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
 * @Target("METHOD")
 */
final class Observe
{
    /** @var string @Required */
    public $event;

    /** @var integer */
    public $priority = 0;

    public function __construct()
    {
        if (0 === func_num_args()) {
            return;
        }
        $values = func_get_arg(0);

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