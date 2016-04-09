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

namespace JMS\DiExtraBundle\Config;

use JMS\DiExtraBundle\Finder\PatternFinder;
use Symfony\Component\Config\Resource\ResourceInterface;
use Symfony\Component\Config\Resource\SelfCheckingResourceInterface;

class ServiceFilesResource extends InternalResource
{
    private $files;
    private $dirs;
    private $disableGrep;
    private $annotationNamespaces;

    public function __construct(array $files, array $dirs, array $annotationNamespaces, $disableGrep)
    {
        $this->files = $files;
        $this->dirs = $dirs;
        $this->annotationNamespaces = $annotationNamespaces;
        $this->disableGrep = $disableGrep;
    }

    public function isFresh($timestamp)
    {
        $files = $this->findFiles($this->dirs, $this->annotationNamespaces, $this->disableGrep);
        return !array_diff($files, $this->files) && !array_diff($this->files, $files);
    }

    private function findFiles(array $directories, array $annotationNamespaces, $disableGrep)
    {
        $files = [];
        foreach ($annotationNamespaces as $namespace) {
            $finder = new PatternFinder($namespace, '*.php', $disableGrep);
            foreach ($finder->findFiles($directories) as $file) {
                $files[$file] = $file;
            }
        }
        return array_values($files);
    }

    public function __toString()
    {
        return implode(', ', $this->files);
    }

    public function getResource()
    {
        return array($this->files, $this->dirs);
    }
}
