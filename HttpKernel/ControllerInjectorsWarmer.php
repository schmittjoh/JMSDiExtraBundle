<?php

namespace JMS\DiExtraBundle\HttpKernel;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class ControllerInjectorsWarmer implements CacheWarmerInterface
{
    private $kernel;
    private $controllerResolver;

    public function __construct(KernelInterface $kernel, ControllerResolver $resolver)
    {
        $this->kernel = $kernel;
        $this->controllerResolver = $resolver;
    }

    public function warmUp($cacheDir)
    {
        // This avoids class-being-declared twice errors when the cache:clear
        // command is called. The controllers are not pre-generated in that case.
        if (basename($cacheDir) === $this->kernel->getEnvironment().'_new') {
            return;
        }

        $classes = $this->findControllerClasses();
        foreach ($classes as $class) {
            $this->controllerResolver->createInjector($class);
        }
    }

    public function isOptional()
    {
        return false;
    }

    private function findControllerClasses()
    {
        $dirs = array();
        foreach ($this->kernel->getBundles() as $bundle) {
            if (!is_dir($controllerDir = $bundle->getPath().'/Controller')) {
                continue;
            }

            $dirs[] = $controllerDir;
        }

        foreach (Finder::create()->name('*Controller.php')->in($dirs)->files() as $file) {
            require_once $file->getRealPath();
        }

        // It is not so important if these controllers never can be reached with
        // the current configuration nor whether they are actually controllers.
        // Important is only that we do not miss any classes.
        return array_filter(get_declared_classes(), function($name) {
            return preg_match('/Controller\\\(.+)Controller$/', $name) > 0;
        });
    }
}
