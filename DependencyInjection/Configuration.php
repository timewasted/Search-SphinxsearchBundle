<?php

namespace Search\SphinxsearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
	/**
	 * Generates the configuration tree.
	 *
	 * @return TreeBuilder
	 */
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('sphinxsearch');

		$this->addIndexerSection($rootNode);
		$this->addIndexesSection($rootNode);
		$this->addSearchdSection($rootNode);

		return $treeBuilder;
	}

	private function addIndexerSection(ArrayNodeDefinition $node)
	{
		$node
			->children()
				->arrayNode('indexer')
					->addDefaultsIfNotSet()
					->children()
						->scalarNode('bin')->defaultValue('/usr/bin/indexer')->end()
					->end()
				->end()
			->end();
	}

	private function addIndexesSection(ArrayNodeDefinition $node)
	{
		$node
			->children()
				->arrayNode('indexes')
					->isRequired()
					->requiresAtLeastOneElement()
					->useAttributeAsKey('key')
					->prototype('array')
						->requiresAtLeastOneElement()
						->useAttributeAsKey('key')
						->prototype('array')
							->useAttributeAsKey('key')
							->prototype('scalar')->end()
						->end()
					->end()
				->end()
			->end();
	}

	private function addSearchdSection(ArrayNodeDefinition $node)
	{
		$node
			->children()
				->arrayNode('searchd')
					->addDefaultsIfNotSet()
					->children()
						->scalarNode('host')->defaultValue('localhost')->end()
						->scalarNode('port')->defaultValue('9312')->end()
						->scalarNode('socket')->defaultNull()->end()
					->end()
				->end()
			->end();
	}
}
