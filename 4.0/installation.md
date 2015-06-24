---
layout: default
title: Installation
---

# Installation

## System Requirements

* **PHP >= 5.5.0** but the latest stable version of PHP is recommended;
* `mbstring` extension;
* `intl` extension;

## Composer

URL is available on [Packagist][] and can be installed using [Composer][]. This can be done by running the following command:

~~~
$ composer require league/url
~~~

Most modern frameworks will include Composer out of the box, but ensure the following file is included:

~~~php
// Include the Composer autoloader
require 'vendor/autoload.php';
~~~

## Going Solo

You can also use `League\Url` without using Composer by downloading the library and using a [PSR-4][] compatible autoloader.

Visit the [releases page][], select the version you want and click the preferred archive download button.

[Packagist]: https://packagist.org/packages/league/url
[Composer]: https://getcomposer.org/
[PSR-4]: http://www.php-fig.org/psr/psr-4/
[releases page]: https://github.com/thephpleague/url/releases