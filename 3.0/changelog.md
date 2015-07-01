---
layout: default
title: Version 3 - Changelog
---

#Changelog

All Notable changes to `League\Url` version 3 will be documented in this file

## 3.3.4 - 2015-07-01

### Fixed
- Bug fix Query parsing [pull request #79](https://github.com/thephpleague/url/pull/79)

## 3.3.3 - 2015-06-25

### Fixed
- Update `True\Punycode` requirement to 2.0.0 to allow PHP7 support

## 3.3.2

### Fixed

- Bug fix URL parsing [issue #65](https://github.com/thephpleague/url/issues/65)

## 3.3.1

### Fixed
- `League\Url\Components\Query` bug fix [issue #58](https://github.com/thephpleague/url/issues/58), improved bug fix [issue #31](https://github.com/thephpleague/url/issues/31)

## 3.3.0

### Added
- adding the `toArray` method to `League\Url\AbstractUrl` to output the URL like PHP native `parse_url` [issue #56](https://github.com/thephpleague/url/issues/56)

### Fixed
- `League\Url\Components\Query` bug fix remove parameter only if the value equals `null` [issue #58](https://github.com/thephpleague/url/issues/58)

## 3.2.1

### Fixed
- `League\Url\AbstractUrl\createFromServer` bug fix handling of `$_SERVER['HTTP_HOST']`

## 3.2.0

### Added
- adding the following methods to `League\Url\AbstractUrl`
    - `getUserInfo`
    - `getAuthority`
    - `sameValueAs`

### Fixed
- `League\Url\Components\Fragment::__toString` encoding symbols according to [RFC3986](http://tools.ietf.org/html/rfc3986#section-3.5)


## 3.1.1

### Fixed
- `parse_str` does not preserve key params

## 3.1.0

### Added
- Adding IDN support using `True\Punycode` package
- The library now **requires** the `mbstring` extension to work.

The following methods were added:

- `League\Url\Components\Host::toAscii`
- `League\Url\Components\Host::toUnicode` as an alias of `League\Url\Components\Host::__toString`

### Fixed
- invalid URI parsing

## 3.0.1

### Fixed
- invalid URI parsing

## 3.0.0

New Release, complete rewrite from `Bakame\Url`