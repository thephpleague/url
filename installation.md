---
layout: default
title: Installation
permalink: installation/
---

# Installation

## System Requirements

* **PHP >= 5.3.0** but the latest stable version of PHP is recommended;
* `mbstring` extension installed **since version 3.1**;

## Composer

URL is available on [Packagist](https://packagist.org/packages/league/url) and can be installed using [Composer](https://getcomposer.org/):

~~~javascript
{
    "require": {
        "league/url": "3.*"
    }
}
~~~

Most modern frameworks will include Composer out of the box, but ensure the following file is included:

~~~php
<?php

// Include the Composer autoloader
require 'vendor/autoload.php';
~~~

## Going Solo

You can also use URL without using Composer by registing an autoloader function:

~~~php
spl_autoload_register(function ($class) {
    $prefix = 'League\\Url\\';
    $base_dir = __DIR__ . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
~~~

Or, use any other [PSR-4](http://www.php-fig.org/psr/psr-4/) compatible autoloader.