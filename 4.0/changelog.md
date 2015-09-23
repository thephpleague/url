---
layout: default
title: Version 4 - Changelog
---

#Changelog

All Notable changes to `League\Url` version 4 will be documented in this file

## 4.0.0

### Added

- `Intl` extension is now required to use the library
- `FileInfo` extension is now required to use the library
- Domain parsing capabilities to `Host` using `jeremykendall/php-domain-parser` package
- `UriParser` to parse an URI according to RFC3986 rules
- `QueryParser` to parse and build a query string according to RFC3986 rules.
- `League\Uri\Schemes\Generic\AbstractUri` to enable better URI extension
- URI Modifiers classes to modify URI objects in an uniform way for interoperability
- A `Data` class to specifically manipulate `data` schemed URI
- A `Http` class to specifically manipulate `http`,`https` schemed URI
- A `Ftp` class to specifically manipulate `ftp` schemed URI
- A `Ws` class to specifically manipulate `ws`, `wss` schemed URI
- A `DataPath` component class to manipulate Data-uri path component
- A `HierarchicalPath` to manipulate Hierarchical-like path component
- Support for IP host

### Fixed

- Handling of legacy hostname suffixed with a "." when using `Url::createFromServer`
- Move namespace from `League\Url` to `League\Uri` to avoid dependency hell

### Deprecated 

- Nothing

### Remove

- Support for `PHP 5.4` and `PHP 5.3`
- `League\Url\Url`, `League\Url\UrlImmutable`, `League\Url\UrlConstants` classes
- Dependency on the `True/php-punycode` library
- Dependency on PHP `parse_url`, `parse_str` and `http_build_query` functions
- Most of the public API is removed :
    - to comply to `RFC3986`;
    - to enable immutable value object;