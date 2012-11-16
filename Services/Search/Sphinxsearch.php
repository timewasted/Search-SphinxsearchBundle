<?php

namespace Search\SphinxsearchBundle\Services\Search;

class Sphinxsearch
{
	/**
	 * @var string $host
	 */
	private $host;

	/**
	 * @var string $port
	 */
	private $port;

	/**
	 * @var string $socket
	 */
	private $socket;

	/**
	 * @var array $indexes
	 *
	 * $this->indexes should look like:
	 *
	 * $this->indexes = array(
	 *   'IndexLabel' => 'Index name as defined in sphinxsearch.conf',
	 *   ...,
	 * );
	 */
	private $indexes;

	/**
	 * @var SphinxClient $sphinx
	 */
	private $sphinx;

	/**
	 * Constructor.
	 *
	 * @param string $host The server's host name/IP.
	 * @param string $port The port that the server is listening on.
	 * @param string $socket The UNIX socket that the server is listening on.
	 * @param array $indexes The list of indexes that can be used.
	 */
	public function __construct($host = 'localhost', $port = '9312', $socket = null, array $indexes = array())
	{
		$this->host = $host;
		$this->port = $port;
		$this->socket = $socket;
		$this->indexes = $indexes;

		$this->sphinx = new \SphinxClient();
		if( $this->socket !== null )
			$this->sphinx->setServer($this->socket);
		else
			$this->sphinx->setServer($this->host, $this->port);
	}

	/**
	 * Escape the supplied string.
	 *
	 * @param string $string The string to be escaped.
	 *
	 * @return string The escaped string.
	 */
	public function escapeString($string)
	{
		return $this->sphinx->escapeString($string);
	}

	/**
	 * Set the desired match mode.
	 *
	 * @param int $mode The matching mode to be used.
	 */
	public function setMatchMode($mode)
	{
		$this->sphinx->setMatchMode($mode);
	}

	/**
	 * Set the desired search filter.
	 *
	 * @param string $attribute The attribute to filter.
	 * @param array $values The values to filter.
	 * @param bool $exclude Is this an exclusion filter?
	 */
	public function setFilter($attribute, $values, $exclude = false)
	{
		$this->sphinx->setFilter($attribute, $values, $exclude);
	}

	/**
	 * Search for the specified query string.
	 *
	 * @param string $query The query string that we are searching for.
	 * @param array $indexes The indexes to perform the search on.
	 * @param array $options The options for the query.
	 * @param bool $escapeQuery Should the query string be escaped?
	 *
	 * @return array The results of the search.
	 */
	public function search($query, array $indexes, array $options = array(), $escapeQuery = true)
	{
		if( $escapeQuery )
			$query = $this->sphinx->escapeString($query);

		/**
		 * Build the list of indexes to be queried.
		 */
		$indexNames = '';
		foreach( $indexes as &$label ) {
			if( isset($this->indexes[$label]) )
				$indexNames .= $this->indexes[$label] . ' ';
		}

		/**
		 * If no valid indexes were specified, return an empty result set.
		 *
		 * FIXME: This should probably throw an exception.
		 */
		if( empty($indexNames) )
			return array();

		/**
		 * Set the offset and limit for the returned results.
		 */
		if( isset($options['result_offset']) && isset($options['result_limit']) )
			$this->sphinx->setLimits($options['result_offset'], $options['result_limit']);

		/**
		 * Weight the individual fields.
		 */
		if( isset($options['field_weights']) )
			$this->sphinx->setFieldWeights($options['field_weights']);

		/**
		 * Perform the query.
		 */
		$results = $this->sphinx->query($query, $indexNames);
		if( $results['status'] !== SEARCHD_OK )
			throw new \RuntimeException(sprintf('Searching index "%s" for "%s" failed with error "%s".', $label, $query, $this->sphinx->getLastError()));

		return $results;
	}
}
