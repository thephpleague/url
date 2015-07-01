# Url

[![Build Status](https://img.shields.io/travis/thephpleague/url/3.x.svg?style=flat-square)](https://travis-ci.org/thephpleague/url)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/thephpleague/url/3.x.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/url/?branch=3.x)
[![Quality Score](https://img.shields.io/scrutinizer/g/thephpleague/url/3.x.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/url/?branch=3.x)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Version](https://img.shields.io/github/release/thephpleague/url.svg?style=flat-square)](https://github.com/thephpleague/url/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/league/url.svg?style=flat-square)](https://packagist.org/packages/league/url)

Url is a simple library to ease creating and managing Urls in PHP.

This package is compliant with [PSR-2][], and [PSR-4][].

[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md

Requirements
-------

You need **PHP >= 5.3.0** and the `mbstring` extension to use the library, but the latest stable version of PHP is recommended.

Install
-------

Install `Url` using Composer.

```
composer require league/url:~3.0
```

This will edit (or create) your `composer.json` file and automatically choose the most recent version in the 3.x serie.

#### Going Solo

You can also use `League\Url` without using Composer by [downloading the library](https://github.com/thephpleague/url/releases) and using a [PSR-4](http://www.php-fig.org/psr/psr-4/) compatible autoloader.

Documentation
-------

`League\Url` is [fully documented](http://url.thephpleague.com). Contribute to this documentation in the [gh-pages branch](https://github.com/thephpleague/url/tree/gh-pages).

Testing
-------

``` bash
$ phpunit
```

Contributing
-------

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

Credits
-------

- [ignace nyamagana butera](https://github.com/nyamsprod)
- [All Contributors](graphs/contributors)

License
-------

The MIT License (MIT). Please see [License File](LICENSE) for more information.
