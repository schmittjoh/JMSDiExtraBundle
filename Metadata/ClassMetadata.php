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

/**
 * class metadata
 */
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
    /**
     * @deprecated since version 1.7, to be removed in 2.0. Use $initMethods instead.
     */
    public $initMethod;
    public $initMethods = array();
    public $environments = array();
    public $decorates;
    public $decoration_inner_name;
    public $deprecated;

    /**
     * @param string $env
     *
     * @return bool
     */
    public function isLoadedInEnvironment($env)
    {
        if (empty($this->environments)) {
            return true;
        }

        return in_array($env, $this->environments, true);
    }

    /**
     * @return string
     */
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
            $this->decorates,
            $this->decoration_inner_name,
            $this->deprecated,
            $this->initMethods,
        ));
    }

    /**
     * @param string $str
     */
    public function unserialize($str)
    {
        $data = unserialize($str);

        // prevent errors if not all key's are set
        @list(
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
            $parentStr,
            $this->environments,
            $this->decorates,
            $this->decoration_inner_name,
            $this->deprecated,
            $this->initMethods,
        ) = $data;

        parent::unserialize($parentStr);
    }
}
