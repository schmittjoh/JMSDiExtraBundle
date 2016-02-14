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
 * processed annotations for service creation
 */
class ClassMetadata extends BaseClassMetadata
{
    /**
     * @var string
     * @deprecated use addService instead
     */
    public $id;

    /**
     * @var string
     * @deprecated use addService instead
     */
    public $parent;

    /**
     * @var string
     * @deprecated use addService instead, removed in SF 3.0
     */
    public $scope;

    /**
     * @var bool
     * @deprecated use addService instead
     */
    public $public;

    /**
     * @var boolean
     * @deprecated use addService instead
     */
    public $abstract;

    /**
     * @var array
     */
    public $tags = array();

    /**
     * constructor arguments
     *
     * @var array
     */
    public $arguments;

    /**
     * @var array
     */
    public $methodCalls = array();

    /**
     * @var array
     */
    public $lookupMethods = array();

    /**
     * @var array
     */
    public $properties = array();
    /**
     * @deprecated since version 1.7, to be removed in 2.0. Use $initMethods instead.
     */
    public $initMethod;
    public $initMethods = array();

    /**
     * @deprecated use addService instead
     *
     * @var string[]
     */
    public $environments = array();

    /**
     * service definitions
     *
     * @var array[]
     */
    private $services = array();

    /**
     * on first call also populate legacy fields
     *
     * @param string[] $service
     */
    public function addService(array $service)
    {
        if (empty($this->id)) {
            $this->id = $service['id'];
            $this->parent = @$service['parent'];
            $this->public = @$service['public'];
            $this->scope = @$service['scope'];
            $this->abstract = @$service['abstract'];
            $this->environments = @$service['environments'];
            // TODO update call for other tags (there are several pull requests)
        }

        $this->services[$service['id']] = $service;
    }

    /**
     * @return bool
     */
    public function hasServices()
    {
        return !empty($this->services);
    }

    /**
     * get list of defined services, use fallback of original fields
     *
     * @return array[]
     */
    public function getServices()
    {
        // TODO remove fallback for next major version
        if (empty($this->services) || !isset($this->services[$this->id])) {
            $this->services[] = array(
                'id' => $this->id,
                'parent' => $this->parent,
                'public' => $this->public,
                'scope' => $this->scope,
                'abstract' => $this->abstract,
                'environments' => $this->environments,
            );
        }

        return $this->services;
    }

    /**
     * @deprecated this is handled on service level
     *
     * @param string $env
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
            $this->services,
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
            $this->services,
        ) = $data;

        parent::unserialize($parentStr);
    }
}
