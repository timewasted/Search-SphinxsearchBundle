Changelog
=========

v1.2.2
------

* Implement `Sphinxsearch::addQuery`.  This is a wrapper for [the official API call](http://sphinxsearch.com/docs/current.html#api-reference) of the same name, with the exception that it translates index labels to index names in the same way that `Sphinxsearch::search` does.

* Implement `Sphinxsearch::setLimits`, `Sphinxsearch::setFieldWeights`, `Sphinxsearch::resetFilters`, and `Sphinxsearch::runQueries`.  These are strictly wrappers for [the official API calls](http://sphinxsearch.com/docs/current.html#api-reference) of the same name.

v1.2.1
------

* Fix the path to `SphinxAPI.php` (really, this time).  It now looks for it at `vendor/search/sphinxsearch-bundle/Search/SphinxsearchBundle/Services/Search/SphinxAPI.php`

v1.2.0
------

* **Backwards compatibility breakage.**

    `Sphinxsearch::search` has been redesigned.  The previous behavior when given multiple indexes was to perform multiple queries, one against each index provided.  Now, only one query will be performed against all indexes provided.  This required a few changes to how the function is called.

    The function definition has changed from this:

    ``` php
public function search($query, array $indexes, $escapeQuery = true)
    ```

    to this:

    ``` php
public function search($query, array $indexes, array $options = array(), $escapeQuery = true)
    ```

    `$indexes` is now a simple array containing only the index labels that are to be queried.  This means that instead of this:

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
    ```

    you will have this:

    ``` php
$indexesToSearch = array(
  'Items',
  'Categories',
);
    ```

    Query options are now passed in via `$options` and are applied on a per-query basis, instead of a per-index basis.  The above example would have an `$options` array of:

    ``` php
$options = array(
  'field_weights' => array(
    'Name' => 5,
    'SKU' => 10,
    'Description' => 1,
  ),
);
    ```

* The bundle now looks for `SphinxAPI.php` in `vendor/bundles/Search/SphinxsearchBundle/Services/Search/SphinxAPI.php` instead of in `src/Search/SphinxsearchBundle/Services/Search/SphinxAPI.php`

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
