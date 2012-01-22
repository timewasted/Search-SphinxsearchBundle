About SphinxsearchBundle
===================

Installation:
-------------

1. Download the bundle
2. Configure the autoloader
3. Enable the bundle
4. Configure the bundle

### Step 1: Download the bundle

How you actually download the bundle is entirely up to you.  However, once downloaded, you should place it in the `src/Search/SphinxsearchBundle` directory.

### Step 2: Configure the autoloader

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
        // ...
        'Search' => __DIR__ . '/../src',
));
```

### Step 3: Enable the bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
        $bundles = array(
                // ...
                new Search\SphinxsearchBundle\SphinxsearchBundle(),
        );
}
```

### Step 4: Configure the bundle

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

At least one index must be defined, and you may define as many as you like.

In the above sample configuration, `Categories` is used as a label for the index named `%sphinxsearch_index_categories%` (as defined in your `sphinx.conf`).  This allows you to avoid having to hard code raw index names inside of your code.  You can also optionally define field weights to be applied when searching.  In the case of the `Items` index, `Description` has a low weight, while `SKU` is weighted significantly higher.

License:
--------

```
Copyright (c) 2012, Ryan Rogers
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met: 

1. Redistributions of source code must retain the above copyright notice, this
   list of conditions and the following disclaimer. 
2. Redistributions in binary form must reproduce the above copyright notice,
   this list of conditions and the following disclaimer in the documentation
   and/or other materials provided with the distribution. 

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
```
