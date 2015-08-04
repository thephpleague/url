---
layout: default
title: The URI Parser
---

# The Parser

Because the library parses URIs and query string in accordance to RFC3986, it ships with its own parser.


## Parsing an URI to extract its components

The URI parser does not depends on PHP's `parse_url` function. To parse an URI according to RFC3986 you need to call the public method `parseUri` which expects a `string`. The method returns a hash representation of the URI similar to `parse_url` results.

The main differences between the `Parser::parseUri` method and the `parse_url` function are highlighted below:

The `Parser::parseUri` method always returns all URI components.

~~~php
use League\Uri\Parser;

$parser = new Parser();
var_dump($parser->parse('http://www.example.com/'));
//returns the following array
array(
    'scheme' => 'http',
    'user' => null,
    'pass' => null,
    'host' => 'www.example.com',
    'port' => null,
    'path' => '/',
    'query' => null,
    'fragment' => null,
);

var_dump(parse_url('http://www.example.com/'));
//returns the following array
array(
    'scheme' => 'http',
    'host' => 'www.example.com',
    'path' => '/',
);
~~~

Since the `Parser::parseUri` method always returns all the components, accessing the individual components is simple without needing extra parameters:

~~~php
use League\Uri\Parser;

$uri = 'http://www.example.com/';
$parser = new Parser();
$parser->parseUri($uri)['query']; //returns null
parse_url($uri, PHP_URL_QUERY); //returns null
~~~

The `Parser::parseUri` method makes a distinction between an unspecified component, which will be set to `null` and an empty component which will be equal to the empty string.

~~~php
use League\Uri\Parser;

$uri = 'http://www.example.com/?';
$parser = new Parser();
$parser->parseUri($uri)['query']; //returns ''
parse_url($uri, PHP_URL_QUERY); //returns `null`
~~~

Since a URI is made of at least a path component, this component is never equal to `null`

~~~php
use League\Uri\Parser;

$uri = 'http://www.example.com?';
$parser = new Parser();
$parser->parseUri($uri)['path']; //returns ''
parse_url($uri, PHP_URL_PATH); //returns `null`
~~~

## Parsing and building the query string

To preserve the query string, the library does not rely on PHP's `parse_str` and `http_build_query` functions.

Instead, the `Parser` provides two public methods that can be used to parse a query string into an array of key value pairs. And conversely creates a valid query string from the resulting array.

### Parsing the query string into an array

- `parse_str` replaces any invalid characters from the query string pair key that can not be included in a PHP variable name by an underscore `_`.
- `parse_str` merges query string values.

These behaviors, specific to PHP, may be considered to be a data loss transformation in other languages.

~~~php
$query_string = 'toto.foo=bar&toto.foo=baz';
parse_str($query_string, $arr);
// $arr is an array containing ["toto_foo" => "baz"]
~~~

To avoid these transformations, the `Parser::parseQuery` method returns an `array` representation of the query string which preserve key/value pairs. The method expects at most 3 arguments:

- The query string;
- The query string separator, by default it is set to `&`;
- The query string encryption. It can be one of PHP constant `PHP_QUERY_RFC3986` or `PHP_QUERY_RFC1738` or `false` if you don't want any encryption. By default it is set to PHP constants `PHP_QUERY_RFC3986`

~~~php
use League\Uri\Parser;

$parser = new Parser();
$query_string = 'toto.foo=bar&toto.foo=baz';
$arr = $parser->parseQuery($query_string, '&', PHP_RFC3986);
// $arr is an array containing ["toto.foo" => [["bar", "baz"]]
~~~

### Building the query string from an array

`http_build_query` always adds array numeric prefix to the query string even when they are not needed

using PHP's `parse_str`

~~~php
$query_string = 'foo[]=bar&foo[]=baz';
parse_str($query_string, $arr);
// $arr = ["foo" => ['bar', 'baz']];

$res = rawurldecode(http_build_query($arr, '', PHP_QUERY_RFC3986));
// $res equals foo[0]=bar&foo[1]=baz
~~~

or using `Parser::parseQuery`

~~~php
use League\Uri\Parser;

$query_string = 'foo[]=bar&foo[]=baz';
$parser = new Parser();
$arr = $parser->parseQuery($query_string, '&', PHP_RFC3986);
// $arr = ["foo[]" => ['bar', 'baz']];

$res = rawurldecode(http_build_query($arr, '', PHP_QUERY_RFC3986));
// $res equals foo[][0]=bar&oo[][1]=baz
~~~

The `Parser::buildQuery` method returns and preserves string representation of the query string from the `Parser::parseQuery` array result. the method expects at most 3 arguments:

- A valid `array` of data to convert;
- The query string separator, by default it is set to `&`;
- The query string encryption. It can be one of PHP constant `PHP_QUERY_RFC3986` or `PHP_QUERY_RFC1738` or `false` if you don't want any encryption. By default it is set to PHP constants `PHP_QUERY_RFC3986`

~~~php
use League\Uri\Parser;

$query_string = 'foo[]=bar&foo[]=baz';
$parser = new Parser();
$arr = $parser->parseQuery($query_string, '&', PHP_RFC3986);
var_export($arr);
// $arr include the following data ["foo[]" => ['bar', 'baz']];

$res =$parser->buildQuery($arr, '&', false);
// $res equals 'foo[]=bar&foo[]=baz'
~~~

No key indexes is added and the query string is safely recreated