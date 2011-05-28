<?php

namespace JMS\DiExtraBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Alias;

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
        $loader->load('controller_injection.xml');

        $this->configureMetadata($config['metadata'], $container);
    }

    private function configureMetadata(array $config, $container)
    {
        if ('none' === $config['cache']) {
            return;
        }

        if ('file' === $config['cache']) {
            $cacheDir = $container->getParameterBag()->resolveValue($config['file_cache']['dir']);
            if (!file_exists($cacheDir)) {
                if (false === @mkdir($cacheDir, 0777, true)) {
                    throw new \RuntimeException(sprintf('The cache dir "%s" could not be created.', $cacheDir));
                }
            }
            if (!is_writable($cacheDir)) {
                throw new \RuntimeException(sprintf('The cache dir "%s" is not writable.', $cacheDir));
            }

            $container
                ->getDefinition('jms_di_extra.metadata.cache.file_cache')
                ->replaceArgument(0, $cacheDir)
            ;
        } else {
            $container->setAlias('jms_di_extra.metadata.cache', new Alias($config['cache'], false));
        }
    }

    private function mergeConfigs(array $configs)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        return $processor->process($configuration->getConfigTreeBuilder()->buildTree(), $configs);
    }
}