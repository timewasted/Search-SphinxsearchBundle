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
	 * Constructor.
	 *
	 * @param string $bin The path to the indexer executable.
	 */
	public function __construct($bin = '/usr/bin/indexer')
	{
		$this->bin = $bin;
	}

	/**
	 * Rebuild and rotate all indexes.
	 */
	public function rotateAll()
	{
		$this->rotate(array('--all'));
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
			foreach( $indexes as &$index )
				$pb->add($index);
		} elseif( is_string($indexes) ) {
			$pb->add($indexes);
		} else {
			throw new \RuntimeException(sprintf('Indexes can only be an array or string, %s given.', gettype($indexes)));
		}

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
