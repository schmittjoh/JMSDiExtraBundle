<?php

namespace JMS\DiExtraBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $tb = new TreeBuilder();

        $tb
            ->root('jms_di_extra', 'array')
                ->children()
                    ->arrayNode('locations')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('all_bundles')->defaultFalse()->end()
                            ->arrayNode('bundles')
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(function($v) {
                                        return preg_split('/\s*,\s*/', $v);
                                    })
                                ->end()
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('directories')
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(function($v) {
                                        return preg_split('/\s*,\s*/', $v);
                                    })
                                ->end()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('metadata')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('cache')->defaultValue('file')->cannotBeEmpty()->end()
                            ->arrayNode('file_cache')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('dir')->defaultValue('%kernel.cache_dir%/diextra')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $tb;
    }
}