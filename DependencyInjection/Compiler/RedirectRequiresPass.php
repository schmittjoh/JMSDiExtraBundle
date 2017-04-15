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

namespace JMS\DiExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Prevents class redeclaration errors from require_once the same class during warmup
 * See https://github.com/schmittjoh/JMSDiExtraBundle/issues/23
 *
 * @author Bertrand Fan <bertrand@fan.net>
 */
class RedirectRequiresPass implements CompilerPassInterface
{
    
    private $kernel;
    private $warmup;

    private static $requiresLookup = array();
    
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->warmup = (substr($this->kernel->getCacheDir(), -4, 4) == '_new');
    }
    
    public function process(ContainerBuilder $container)
    {

        foreach ($container->getDefinitions() as $id => $definition) {
            
            if ($file = $definition->getFile()) {

                $class = $definition->getClass() ? $definition->getClass() : $definition->getFile();

                if (!$this->warmup) {
                    self::$requiresLookup[$class] = $file;
                    continue;
                }
                
                if (isset(self::$requiresLookup[$class]) && self::$requiresLookup[$class] != $definition->getFile()) {
                    $definition->setFile(self::$requiresLookup[$class]);
                }

            }

        }

    }

}
