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

use JMS\DiExtraBundle\Metadata\ClassMetadata;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Admin implements MetadataProcessorInterface
{
    /** @var string @Required */
    public $class;

    /** @var string */
    public $managerType = 'orm';

    /** @var string */
    public $group;

    /** @var string */
    public $label;

    /** @var string */
    public $code;

    /** @var string */
    public $baseControllerName = 'SonataAdminBundle:CRUD';

    /** @var string */
    public $translationDomain;

    public function processMetadata(ClassMetadata $metadata)
    {
        $properties = $this->generateAdminProperties($this->class);

        if (!$properties && (!$this->group || !$this->label)) {
            throw new \RuntimeException(sprintf("Unable to generate admin group and label for class %s. Please define custom.", $metadata->name));
        }

        $metadata->tags['sonata.admin'][] = array(
            'manager_type' => $this->managerType,
            'group' => $this->group ?: $properties[0],
            'label' => $this->label ?: $properties[1],
        );

        $metadata->arguments = array(
            $this->code,
            $this->class,
            $this->baseControllerName,
        );

        if ($this->translationDomain) {
            $metadata->methodCalls[] = array('setTranslationDomain', array($this->translationDomain));
        }
    }

    private function generateAdminProperties($name)
    {
        $matches = array();

        preg_match('@[A-Za-z0-9]+\\\([A-Za-z0-9]+)Bundle\\\(Document|Entity)\\\([A-Za-z0-9]+)@', $name, $matches);

        if (!$matches) {
            return null;
        }

        return array($matches[1], $matches[3]);
    }
}
