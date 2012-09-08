Changelog
=========

v1.1.0
------

* **Backwards compatibility breakage.**

    Instead of specifying field weights globally in `config.yml`, they are now configured in-app on a per-search basis.  This changes the default configuration from something like:

    ``` yaml
sphinxsearch:
    indexes:
        Categories:
            %sphinxsearch_index_categories%: ~
        Items:
            %sphinxsearch_index_items%:
                Name:        5
                SKU:         10
                Description: 1
    searchd:
        host:   %sphinxsearch_host%
        port:   %sphinxsearch_port%
        socket: %sphinxsearch_socket%
    indexer:
        bin:    %sphinxsearch_indexer_bin%
    ```

    to this:

    ``` yaml
sphinxsearch:
    indexes:
        Categories: %sphinxsearch_index_categories%
        Items:      %sphinxsearch_index_items%
    searchd:
        host:   %sphinxsearch_host%
        port:   %sphinxsearch_port%
        socket: %sphinxsearch_socket%
    indexer:
        bin:    %sphinxsearch_indexer_bin%
    ```

    The code to search an index then changes from this:

    ``` php
$indexesToSearch = array(
  'Items' => array(),
  'Categories' => array(),
);
$sphinxSearch = $this->get('search.sphinxsearch.search');
$searchResults = $sphinxSearch->search('search.query', $indexesToSearch);
    ```

    to this:

    ``` php
$indexesToSearch = array(
  'Items' => array(
    'field_weights' => array(
      'Name' => 5,
      'SKU' => 10,
      'Description' => 1,
    ),
  ),
  'Categories' => array(),
);
$sphinxSearch = $this->get('search.sphinxsearch.search');
$searchResults = $sphinxSearch->search('search.query', $indexesToSearch);
    ```

* Added a third, optional parameter to `Sphinxsearch::search`: `$escapeQuery`.  If set to true (which is the default), it will escape the query string that was passed in before handing it off to Sphinx.  Also added `Sphinxsearch::escapeString($string)` to compliment this change.  This allows one to more easily use the more advanced query syntaxes of [the various matching modes](http://sphinxsearch.com/docs/current.html#matching-modes)

* Added `composer.json`.
