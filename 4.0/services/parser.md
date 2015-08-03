---
layout: default
title: The URI Parser
---

# The Parser

The library uses its own internal URI parser and does not depends on PHP's `parse_url` function. The parser is compliant with RFC3986 and exposes only one public method `parse` which expects a `string`. The method returns a hash representation of the URI similar to `parse_url` results.

The main differences between the `Parser::parse` method and the `parse_url` function are highlighted below:

The `Parser::parse`method always returns all URI components.

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

Since the `Parser::parse` method always returns all the components, accessing the individual components is simple without needing extra parameters:

~~~php
use League\Uri\Parser;

$uri = 'http://www.example.com/';
$parser = new Parser();
$parser->parse($uri)['query']; //returns null
parse_url($uri, PHP_URL_QUERY); //returns null
~~~

The `Parser::parse` method makes a distinction between an unspecified component, which will be set to `null` and an empty component which will be equal to the empty string.

~~~php
use League\Uri\Parser;

$uri = 'http://www.example.com/?';
$parser = new Parser();
$parser->parse($uri)['query']; //returns ''
parse_url($uri, PHP_URL_QUERY); //returns `null`
~~~

Since a URI is made of at least a path component, this component is never equal to `null`

~~~php
use League\Uri\Parser;

$uri = 'http://www.example.com?';
$parser = new Parser();
$parser->parse($uri)['path']; //returns ''
parse_url($uri, PHP_URL_PATH); //returns `null`
~~~
