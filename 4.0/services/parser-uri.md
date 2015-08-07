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

## Building an URI from its components

Conversely, If you want to create an URI from its components you can rely on the `UriParser::build` method to generate such string. The method accepts an hash representation of the URI similar to `parse_url` results. So it converts the hash back to the URI string.

~~~php
use League\Uri\UriParser;

$uri = 'http://www.example.com?';
$parser = new UriParser();
$parser->build($parser->parse($uri)); //returns 'http://www.example.com?'
~~~

The provided array **must not** contain all the URI components. The missing key will be treated as being not defined.

~~~php
use League\Uri\UriParser;

$uri = 'http://www.example.com?';
$parser = new UriParser();
$parser->build(parse_url($uri)); //returns 'http://www.example.com' (because of parse_url!!)
~~~

The generated URI string won't be normalized but each component is validated against RFC3986 rules. This means that the `UriParser::build` can throw exceptions.

<p class="message-notice">An <code>InvalidArgumentException</code> exception will be thrown if a component is invalid</p>

~~~php
use League\Uri\UriParser;

$parser = new UriParser();
$parser->build([
    'scheme' => 'http',
    'host' => '--toto.com', //the host is invalid
    'path' => 'path'
]);
~~~

<p class="message-notice">An <code>RuntimeException</code> exception will be thrown if the resulting URI is invalid</p>


~~~php
use League\Uri\UriParser;

$parser = new UriParser();
$parser->build([
    'path' => 'path:toto', // the path can not contains ":" befoe a "/"
                           // if the scheme AND the authority are absent!!
    'query' => 'query',
]);
~~~
