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

namespace JMS\DiExtraBundle\Tests\Functional;

// get the autoload file
$dir = __DIR__;
$lastDir = null;
while ($dir !== $lastDir) {
    $lastDir = $dir;

    if (file_exists($dir.'/autoload.php')) {
        require_once $dir.'/autoload.php';
        break;
    }

    if (file_exists($dir.'/autoload.php.dist')) {
        require_once $dir.'/autoload.php.dist';
        break;
    }

    if (file_exists($dir.'/vendor/autoload.php')) {
        require_once $dir.'/vendor/autoload.php';
        break;
    }

    $dir = dirname($dir);
}

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    private $config;

    public function __construct($config, $debug = true)
    {
        parent::__construct('test', $debug);

        $fs = new Filesystem();
        if (!$fs->isAbsolutePath($config)) {
            $config = __DIR__.'/config/'.$config;
        }

        if (!file_exists($config)) {
            throw new \RuntimeException(sprintf('The config file "%s" does not exist.', $config));
        }

        $this->config = $config;
    }

    public function registerBundles()
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \JMS\AopBundle\JMSAopBundle(),
            version_compare(PHP_VERSION, '7.0.0', '<')?
            new \JMS\DiExtraBundle\Tests\Functional\Bundle\LegacyTestBundle\JMSDiExtraTestBundle():
            new \JMS\DiExtraBundle\Tests\Functional\Bundle\TestBundle\JMSDiExtraTestBundle(),
            new \JMS\DiExtraBundle\JMSDiExtraBundle(),
            new \JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->config);
        if (PHP_VERSION_ID > 50400) {
            $loader->load(__DIR__.'/config/traits.yml');
        }
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'/JMSDiExtraBundle/'.substr(sha1($this->config), 0, 6);
    }

    public function serialize()
    {
        return serialize(array($this->config, $this->isDebug()));
    }

    public function unserialize($str)
    {
        call_user_func_array(array($this, '__construct'), unserialize($str));
    }
}
