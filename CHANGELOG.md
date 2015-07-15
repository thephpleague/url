#Changelog

All Notable changes to `League\Url` will be documented in this file

## 3.3.5 - 2015-07-15

### Fixed
- Bug fix path get relative [pull request #81](https://github.com/thephpleague/url/pull/81)

## 3.3.4 - 2015-07-01

### Fixed
- Bug fix Query parsing [pull request #79](https://github.com/thephpleague/url/pull/79)

## 3.3.3 - 2015-06-25

### Fixed
- Update `True\Punycode` requirement to 2.0.0 to allow PHP7 support

## 3.3.2 - 2015-05-13

### Fixed

- Bug fix URL parsing [issue #65](https://github.com/thephpleague/url/issues/58)

## 3.3.1 - 2015-03-26

### Fixed
- `League\Url\Components\Query` bug fix [issue #58](https://github.com/thephpleague/url/issues/58), improved bug fix [issue #31](https://github.com/thephpleague/url/issues/31)

## 3.3.0 - 2015-03-20

### Added
- adding the `toArray` method to `League\Url\AbstractUrl` to output the URL like PHP native `parse_url` [issue #56](https://github.com/thephpleague/url/issues/56)

### Fixed
- `League\Url\Components\Query` bug fix remove parameter only if the value equals `null` [issue #58](https://github.com/thephpleague/url/issues/58)

## 3.2.1 - 2014-11-27

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- `League\Url\AbstractUrl\createFromServer` bug fix handling of `$_SERVER['HTTP_HOST']`

### Remove
- Nothing

### Security
- Nothing

## 3.2.0 - 2014-11-12

### Added
- adding the following methods to `League\Url\AbstractUrl`
    - `getUserInfo`
    - `getAuthority`
    - `sameValueAs`

### Deprecated
- Nothing

### Fixed
- `League\Url\Components\Fragment::__toString` encoding symbols according to [RFC3986](http://tools.ietf.org/html/rfc3986#section-3.5)

### Remove
- Nothing

### Security
- Nothing

## 3.1.1 - 2014-09-02

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- `parse_str` does not preserve key params

### Remove
- Nothing

### Security
- Nothing

## 3.1.0 - 2014-07-10

### Added
- Adding IDN support using `True\Punycode` package
- The library now **requires** the `mbstring` extension to work.

The following methods were added:

- `League\Url\Components\Host::toAscii`
- `League\Url\Components\Host::toUnicode` as an alias of `League\Url\Components\Host::__toString`

### Deprecated
- Nothing

### Fixed
- invalid URI parsing

### Remove
- Nothing

### Security
- Nothing

## 3.0.1 - 2014-06-31

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- invalid URI parsing

### Remove
- Nothing

### Security
- Nothing

## 3.0 - 2014-06-25

New Release, complete rewrite from `Bakame\Url`
