---
layout: default
title: Version 4 - Changelog
---

#Changelog

All Notable changes to `League\Url` version 4 will be documented in this file

## Next

### Added

- A system to manage registration of other schemes using the `SchemeRegistry` Interface.
- Added default support for the following schemes: `ldap`, `ldaps`, `nntp`, `snews`, `telnet`, `wais`
- Support for IPv6 zone identifier
- Intl extension is now required to use the library

### Remove

- `Scheme::isSupported`
- `Port::getStandardSchemes`
- `Scheme::getStandardPort` use the `SchemeRegistry` class to get this information.
- `Scheme::hasStandardPort` use the `SchemeRegistry` class to get this information.
- support for `PHP 5.4`

## 4.0.0-beta.3

### Added

- `isEmpty` method to `League\Url\Interfaces\Url` to tell whether a URL is empty or not
- `isSupported` static method to `League\Url\Scheme` to tell whether a specified scheme is supported by the library
- Add support for `gopher` scheme

### Fixed

- Invalid Punycode should still be allowed and not produce any error [issue #73](https://github.com/thephpleague/url/issues/73)

### Remove

 - Remove support for `git` and `svn` schemes

## 4.0.0-beta.2

### Fixed
- remove useless optional argument from `Path::getUriComponent`

## 4.0.0-beta.1

### Added

- Package structure is changed to better reflect the importance of each component.

- `League\Url\Interfaces\Url`
    -  now implements `Psr\Http\Message\UriInterface`
    - `resolve` to create new URL from relative URL
    - `isAbsolute` tells whether the URL is absolute or relative
    - `hasStandardPort`  tells whether the URL uses the standard port for a given scheme
    - `sameValueAs` accepts any `Psr\Http\Message\UriInterface` implementing object
    - add proxy methods to ease partial component modifications

- `League\Url\Interfaces\UrlPart`
    -  UrlParts implementing object can be compared using the `sameValueAs`

- `League\Url\Interfaces\Component`
    - `modify` to create a new instance from a given component;

- `League\Url\Interfaces\CollectionComponent`:
    - The interface is simplified to remove ambiguity when manipulating Host and Path objects.

- `League\Url\Interfaces\Host`:
    - implements IPv4 and IPv6 style host
    - `__toString` method now always return the ascii version of the hostname

- `League\Url\Interfaces\Path`:
    - `withoutDotSegment` remove dot segment according to RFC3986 rules;
    - `withoutEmptySegments` remove multiple adjacent delimiters;
    - `getBasename` returns the trailing path;
    - `getDirname` returns the parent directory path;
    - manage the trailing path extension using `getExtension` and `withExtension`;

- `League\Url\Interfaces\Query`:
    - The interface is simplified to remove ambiguity and allow setting default values for missing keys;
    - The object no longer depends on php `parse_str`

- `League\Url\Interfaces\Scheme` and `League\Url\Interfaces\Port`:
    - support for listing and detecting standard port for a given scheme in both objects with
        - `Interfaces\Port::getStandardSchemes`
        - `Interfaces\Scheme::getStandardPorts`
        - `Interfaces\Scheme::hasStandardPort`

- `League\Url\UserInfo` class added to better manipulate URL user info part

- The `Url` class as well as all components classes are now immutable value objects.
- The `League\Url\Output\Formatter` class is added to ease Url formatting
- The package is more RFC3986 compliant

### Deprecated
- Nothing

### Fixed
- Handling of legacy hostname suffixed with a "." when using `Url::createFromServer`

### Remove
- `League\Url\Components\User` and `League\Url\Components\Pass`
- Support for `PHP 5.3`
- `UrlImmutable` class
- Most of the public API is removed :
    - to comply to `RFC3986`;
    - to enable immutable value object;
    - to implement `Psr\Http\Message\UriInterface`;