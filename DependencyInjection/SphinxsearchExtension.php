<?php

namespace Search\SphinxsearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SphinxsearchExtension extends Extension
{
	public function load(array $configs, ContainerBuilder $container)
	{
		$processor = new Processor();
		$configuration = new Configuration();

		$config = $processor->processConfiguration($configuration, $configs);

		$loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

		$loader->load('sphinxsearch.xml');

		/**
		 * Indexer.
		 */
		if( isset($config['indexer']) ) {
			$container->setParameter('search.sphinxsearch.indexer.bin', $config['indexer']['bin']);
		}

		/**
		 * Indexes.
		 */
		$indexes = array();
		foreach( $config['indexes'] as $label => $index ) {
			foreach( $index as $name => $fields ) {
				if( !isset($indexes[$label]) )
					$indexes[$label] = array('index_name' => $name, 'field_weights' => array());

				foreach( $fields as $field => $weight )
					$indexes[$label]['field_weights'][$field] = $weight;
			}
		}
		$container->setParameter('search.sphinxsearch.indexes', $indexes);

		/**
		 * Searchd.
		 */
		if( isset($config['searchd']) ) {
			$container->setParameter('search.sphinxsearch.searchd.host', $config['searchd']['host']);
			$container->setParameter('search.sphinxsearch.searchd.port', $config['searchd']['port']);
			$container->setParameter('search.sphinxsearch.searchd.socket', $config['searchd']['socket']);
		}
	}

	public function getAlias()
	{
		return 'sphinxsearch';
	}
}
