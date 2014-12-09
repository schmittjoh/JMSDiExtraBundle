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
    public $arguments;
    public $methodCalls = array();
    public $lookupMethods = array();
    public $properties = array();
    public $initMethod;
    public $environments = array();

    public function isLoadedInEnvironment($env)
    {
        if (empty($this->environments)) {
            return true;
        }

        return in_array($env, $this->environments, true);
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->parent,
            $this->scope,
            $this->public,
            $this->abstract,
            $this->tags,
            $this->arguments,
            $this->methodCalls,
            $this->lookupMethods,
            $this->properties,
            $this->initMethod,
            parent::serialize(),
            $this->environments,
        ));
    }

    public function unserialize($str)
    {
        $data = unserialize($str);

        list(
            $this->id,
            $this->parent,
            $this->scope,
            $this->public,
            $this->abstract,
            $this->tags,
            $this->arguments,
            $this->methodCalls,
            $this->lookupMethods,
            $this->properties,
            $this->initMethod,
            $parentStr
        ) = $data;

        if (isset($data[12])) {
            $this->environments = $data[12];
        }

        parent::unserialize($parentStr);
    }
}
