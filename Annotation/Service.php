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
final class Service
{
    /** @var string */
    public $id;

    /** @var string */
    public $parent;

    /** @var boolean */
    public $public;

    /** @var string */
    public $scope;

    /** @var boolean */
    public $abstract;

    public function __construct()
    {
        if (0 === func_num_args()) {
            return;
        }
        $values = func_get_arg(0);

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