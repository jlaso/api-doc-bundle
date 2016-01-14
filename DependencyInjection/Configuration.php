<?php

namespace JLaso\ApiDocBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('jlaso_api_doc');

        $rootNode
            ->children()
                ->scalarNode('output_folder')
                    ->isRequired()
                ->end()
                ->arrayNode('managed_locales')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')
                ->end()
            ->end();

        $rootNode
            ->children()
                ->scalarNode('assets_folder')
                ->end()
                ->scalarNode('title')
                ->end()
            ->end();

        return $treeBuilder;
    }
}