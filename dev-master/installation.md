---
layout: default
title: Installation
---

# Installation

## System Requirements

* **PHP >= 5.4.7** but the latest stable version of PHP is recommended;
* `mbstring` extension;

## Composer

URL is available on [Packagist](https://packagist.org/packages/league/url) and can be installed using [Composer](https://getcomposer.org/):

~~~
$ composer require league/url
~~~

Most modern frameworks will include Composer out of the box, but ensure the following file is included:

~~~php
<?php

// Include the Composer autoloader
require 'vendor/autoload.php';
~~~

## Going Solo

You can also use `League\Url` without using Composer by downloading the library and using a [PSR-4](http://www.php-fig.org/psr/psr-4/) compatible autoloader.
