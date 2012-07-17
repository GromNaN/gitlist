<?php

namespace GitList;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Config implements ConfigurationInterface
{
    protected $data;

    public function __construct($file)
    {
        if (!file_exists($file)) {
            die("Please, create the config.ini file.");
        }

        $data = parse_ini_file('config.ini', true);

        $processor = new Processor();
        $this->data = $processor->processConfiguration($this, array('gitlist' => $data));
    }

    public function get($section, $option)
    {
        if (!array_key_exists($section, $this->data)) {
            return false;
        }

        if (!array_key_exists($option, $this->data[$section])) {
            return false;
        }

        return $this->data[$section][$option];
    }

    public function getSection($section)
    {
        if (!array_key_exists($section, $this->data)) {
            return false;
        }

        return $this->data[$section];
    }

    public function set($section, $option, $value)
    {
        $this->data[$section][$option] = $value;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('gitlist', 'array')->children()
            ->arrayNode('git')
                ->children()
                    ->scalarNode('client')->defaultValue('/usr/bin/git')->end()
                    ->scalarNode('repositories')->cannotBeEmpty()->end()
                ->end()
            ->end()
            ->arrayNode('app')
                ->defaultValue(array())
                ->prototype('scalar')->end()
            ->end()
            ->arrayNode('filetypes')
            ->end()
        ->end();

        return $treeBuilder;
    }
}