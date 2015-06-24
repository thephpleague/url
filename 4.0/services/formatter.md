---
layout: default
title: The URL Formatter
---

# The Formatter

The Formatter service class helps you format your URL according to your output.

## Formatter Properties

### Host encoding strategy

A host can be output as encoded in ascii or in unicode. By default the formatter encode the host in unicode. To set the encoding you need to specify one of the predefined constant:

- `Formatter::HOST_AS_UNICODE` to set the host encoding to IDN;
- `Formatter::HOST_AS_ASCII`   to set the host encoding to ascii;

~~~php
use League\Url\Host;
use League\Url\Services\Formatter;

$formatter = new Formatter();
$formatter->setHostEncoding(Formatter::HOST_AS_UNICODE);
echo $formatter->getHostEncoding(); //display the value of Formatter::HOST_AS_ASCII

$host = new Host('рф.ru');
echo $host;                     //displays 'xn--p1ai.ru'
echo $formatter->format($host); //displays 'рф.ru'
~~~

### Query encoding strategy

A `League\Url\Query` object is by default encoded by following RFC 3986. If you need to change this encoding to the old RFC 1738, you just need to update the query encoding as shown below using the following predefined constant:

- `PHP_QUERY_RFC3986` to set the query encoding as per RFC 3986;
- `PHP_QUERY_RFC1738` to set the query encoding as per RFC 1738;

~~~php
use League\Url\Query
use League\Url\Services\Formatter;

$formatter = new Formatter();
$formatter->setQueryEncoding(PHP_QUERY_RFC1738);
echo $formatter->getQueryEncoding(); //display the value of PHP_QUERY_RFC1738;

$query = Query::createFromArray(['foo' => 'ba r', "baz" => "bar"]);
echo $query; //displays foo=ba%20&baz=bar
echo $formatter->format($query); //displays foo=ba+r&baz=bar
~~~

### Modifying the query separator

~~~php
use League\Url\Query
use League\Url\Services\Formatter;

$formatter = new Formatter();
$formatter->setQuerySeparator('&amp;');
echo $formatter->getQuerySeparator(); //returns &amp;
$query = Query::createFromArray(['foo' => 'ba r', "baz" => "bar"]);
echo $query; //displays foo=ba%20&baz=bar
echo $formatter->format($query); //displays foo=ba%20r&amp;baz=bar
~~~

### Extending the Formatter capability by attaching a SchemeRegistry object

Just like the [Scheme component](/4.0/components/scheme/) you can manage the object scheme support using the library [scheme registry system](/4.0/services/scheme-registration/).

Using the constructor you can optionally provide a `SchemeRegistry` object.

~~~php
use League\Url\Services\Formater;
use League\Url\Services\SchemeRegistry;

$registry    = new SchemeRegistry();
$newRegistry = $registry->merge(['yolo' => 8080]);
$formatter   = new Formatter($newRegistry);
~~~

You can also use the `setSchemeRegistry` method:

~~~php
use League\Url\Services\Formater;
use League\Url\Services\SchemeRegistry;

$registry    = new SchemeRegistry();
$newRegistry = $registry->merge(['yolo' => 8080]);
$formatter   = new Formatter();
$formatter->setSchemeRegistry($newRegistry);
~~~

You can access this registry at any given time using its getter method

~~~php
use League\Url\Services\Formater;
use League\Url\Services\SchemeRegistry;

$registry    = new SchemeRegistry();
$newRegistry = $registry->merge(['yolo' => 8080]);
$formatter   = new Formatter();
$formatter->setSchemeRegistry($newRegistry);
$altRegistry = $formatter->getSchemeRegistry();
//$altRegistry and $newRegistry are the same
~~~

## Using the Formatter with a complete URL

Apart form URL component class, the `Formatter::format` method can modify the string representation of:

- any `League\Url\*` objects.
- any string or object which exposes a `__toString` method like any class implementing the PSR-7 UriInterface` class.

### Concrete example

~~~php
use League\Url\Url;
use League\Url\Services\Formatter;

$formatter = new Formatter();
$formatter->setHostEncoding(Formatter::HOST_AS_UNICODE);
$formatter->setQueryEncoding(PHP_QUERY_RFC3986);
$formatter->setQuerySeparator('&amp;');

$url        = Url::createFromUrl('https://рф.ru:81?foo=ba%20r&baz=bar');
$url_string = $formatter->format($url);
echo $url_string; //displays https://рф.ru:81?foo=ba%20r&amp;baz=bar
echo $url;        //displays https://xn--p1ai.ru:81?foo=ba%20r&baz=bar
~~~