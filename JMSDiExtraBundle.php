<?php

namespace JMS\DiExtraBundle;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use JMS\DiExtraBundle\DependencyInjection\Compiler\AnnotationConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class JMSDiExtraBundle extends Bundle
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function build(ContainerBuilder $container)
    {
        $config = $container->getCompiler()->getPassConfig();
        $passes = $config->getBeforeOptimizationPasses();
        array_unshift($passes, new AnnotationConfigurationPass($this->kernel));
        $config->setBeforeOptimizationPasses($passes);
    }
}