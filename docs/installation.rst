.. index::
   single: Installation

Installation
============

To install the package, put the following in your composer.json:

.. code-block:: json

    "require": {
        "espadav8/closure-table": "4.*"
    }


And to ``app/config/app.php``:

.. code-block:: php

    'providers' => array(
        // ...
        'EspadaV8\ClosureTable\ClosureTableServiceProvider',
    ),
