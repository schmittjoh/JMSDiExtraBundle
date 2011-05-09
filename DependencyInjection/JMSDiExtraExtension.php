<?php

namespace JMS\DiExtraBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class JMSDiExtraExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->mergeConfigs($configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $this->configureMetadata($config['metadata'], $container);
    }

    private function configureMetadata(array $config, $container)
    {
        if ('none' === $config['cache']) {
            return;
        }

        if ('file' === $config['cache']) {
        }
    }

    private function mergeConfigs(array $configs)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        return $processor->process($configuration->getConfigTreeBuilder()->buildTree(), $configs);
    }
}