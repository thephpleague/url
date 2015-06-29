---
layout: default
title: Installation
---

# Installation

## System Requirements

* **PHP >= 5.5.0** but the latest stable version of PHP is recommended;
* `mbstring` extension;
* `intl` extension;

## Install

`Url` is available on [Packagist][] and must be installed using [Composer][]. This can be done by running the following command on a composer installed box:

~~~
$ composer require league/url
~~~

Most modern frameworks will include Composer out of the box, but ensure the following file is included:

~~~php
// Include the Composer autoloader
require 'vendor/autoload.php';
~~~

[Packagist]: https://packagist.org/packages/league/url
[Composer]: https://getcomposer.org/