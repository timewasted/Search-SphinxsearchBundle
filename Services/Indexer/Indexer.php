<?php

namespace Search\SphinxsearchBundle\Services\Indexer;

use Assetic\Util\ProcessBuilder;

class Indexer
{
	/**
	 * @var string $bin
	 */
	private $bin;

	/**
	 * @var array $indexes
	 *
	 * $this->indexes should have the format:
	 *
	 *	$this->indexes = array(
	 *		'IndexLabel' => array(
	 *			'index_name'	=> 'IndexName',
	 *			'field_weights'	=> array(
	 *				'FieldName'	=> (int)'FieldWeight',
	 *				...,
	 *			),
	 *		),
	 *		...,
	 *	);
	 */
	private $indexes;

	/**
	 * Constructor.
	 *
	 * @param string $bin The path to the indexer executable.
	 * @param array $indexes The list of indexes that can be used.
	 */
	public function __construct($bin = '/usr/bin/indexer', array $indexes = array())
	{
		$this->bin = $bin;
		$this->indexes = $indexes;
	}

	/**
	 * Rebuild and rotate all indexes.
	 */
	public function rotateAll()
	{
		$this->rotate(array_keys($this->indexes));
	}

	/**
	 * Rebuild and rotate the specified index(es).
	 *
	 * @param array|string $indexes The index(es) to rotate.
	 */
	public function rotate($indexes)
	{
		$pb = new ProcessBuilder();
		$pb
			->inheritEnvironmentVariables()
			->add($this->bin)
			->add('--rotate')
		;
		if( is_array($indexes) ) {
			foreach( $indexes as &$label ) {
				if( isset($this->indexes[$label]) )
					$pb->add($this->indexes[$label]['index_name']);
			}
		} elseif( is_string($indexes) ) {
			if( isset($this->indexes[$indexes]) )
				$pb->add($this->indexes[$indexes]['index_name']);
		} else {
			throw new \RuntimeException(sprintf('Indexes can only be an array or string, %s given.', gettype($indexes)));
		}
		/**
		 * FIXME: Throw an error if no valid indexes were provided?
		 */

		$indexer = $pb->getProcess();
		$code = $indexer->run();

		if( ($errStart = strpos($indexer->getOutput(), 'FATAL:')) !== false ) {
			if( ($errEnd = strpos($indexer->getOutput(), "\n", $errStart)) !== false )
				$errMsg = substr($indexer->getOutput(), $errStart, $errEnd);
			else
				$errMsg = substr($indexer->getOutput(), $errStart);
			throw new \RuntimeException(sprintf('Error rotating indexes: "%s".', rtrim($errMsg)));
		}
	}
}
