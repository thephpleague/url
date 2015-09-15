---
layout: default
title: The URI Parser
---

# The URI Parser

Because the library parses URIs in accordance to RFC3986, it ships with it own URI parser.

## Parsing an URI to extract its components

The URI parser does not depends on PHP's `parse_url` function. To parse an URI according to RFC3986 you need to call the public method `League\Uri\UriParser::parse` which expects a `string`. The method returns a hash representation of the URI similar to `parse_url` results.

The main differences between the `UriParser::parse` method and the `parse_url` function are highlighted below:

The `UriParser::parse` method always returns all URI components.

~~~php
use League\Uri\UriParser;

$parser = new UriParser();
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

Since the `UriParser::parse` method always returns all the components, accessing the individual components is simple without needing extra parameters:

~~~php
use League\Uri\UriParser;

$uri = 'http://www.example.com/';
$parser = new UriParser();
$parser->parse($uri)['query']; //returns null
parse_url($uri, PHP_URL_QUERY); //returns null
~~~

The `UriParser::parse` method makes a distinction between an unspecified component, which will be set to `null` and an empty component which will be equal to the empty string.

~~~php
use League\Uri\UriParser;

$uri = 'http://www.example.com/?';
$parser = new UriParser();
$parser->parse($uri)['query']; //returns ''
parse_url($uri, PHP_URL_QUERY); //returns `null`
~~~

Since a URI is made of at least a path component, this component is never equal to `null`

~~~php
use League\Uri\UriParser;

$uri = 'http://www.example.com?';
$parser = new UriParser();
$parser->parse($uri)['path']; //returns ''
parse_url($uri, PHP_URL_PATH); //returns `null`
~~~

<p class="message-notice">The <code>UriParser</code> class only parse and extract from the URI string its components. You still need to validate them against its scheme specific rules.</p>

~~~php
use League\Uri\UriParser;

$uri = 'http:www.example.com';
$parser = new UriParser();
var_dump($parser->parse($uri));
//returns the following array
array(
    'scheme' => 'http',
    'user' => null,
    'pass' => null,
    'host' => null,
    'port' => null,
    'path' => 'www.example.com',
    'query' => null,
    'fragment' => null,
);
~~~

This invalid HTTP URI will be succefully parsed by the class.
