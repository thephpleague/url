---
layout: default
title: The URL Formatter
---

# The Formatter

The `League\Url\Services\Formatter` utility class helps you format an URL or one of its component for output format like HTML.

## Formatter Properties

### Host encoding strategy

A host can be output as encoded in ascii or in unicode. By default the formatter encode the host in unicode. To set the encoding you need to specify one of the predefined constant:

- `Formatter::HOST_AS_UNICODE` to set the host encoding to IDN;
- `Formatter::HOST_AS_ASCII`   to set the host encoding to ascii;

~~~php
use League\Url\Services\Formatter;

$formatter = new Formatter();
$formatter->setHostEncoding(Formatter::HOST_AS_ASCII);
echo $formatter->getHostEncoding(); //display the value of Formatter::HOST_AS_ASCII
~~~

### Query encoding strategy

A `League\Url\Query` object is by default encoded by following RFC 3986. If you need to change this encoding to the old RFC 1738, you just need to update the query encoding as shown below using the following predefined constant:

- `PHP_QUERY_RFC3986` to set the query encoding as per RFC 3986;
- `PHP_QUERY_RFC1738` to set the query encoding as per RFC 1738;

~~~php
use League\Url\Services\Formatter;

$formatter = new Formatter();
$formatter->setQueryEncoding(PHP_QUERY_RFC1738);
echo $formatter->getQueryEncoding(); //display the value of PHP_QUERY_RFC1738;
~~~

### Modifying the query separator

~~~php
use League\Url\Services\Formatter;

$formatter = new Formatter();
$formatter->setQuerySeparator('&amp;');
echo $formatter->getQuerySeparator(); //returns &amp;
~~~

## Applying the settings to your objects.

Once your Formatter object instantiated and configured, you can output a string representation of:

- any `League\Url` objects using the `Formatter::format` method.
- any string or object which exposes a `__toString` method.

### Concrete example

~~~php
use League\Url\Host;
use League\Url\Services\Formatter;
use League\Url\Query;
use League\Url\Url;

$formatter = new Formatter();
$formatter->setHostEncoding(Formatter::HOST_AS_ASCII);
$formatter->setQueryEncoding(PHP_QUERY_RFC3986);
$formatter->setQuerySeparator('&amp;');

$query        = Query::createFromArray(['foo' => 'ba r', "baz" => "bar"]);
$query_string = $formatter->format($query);
echo $query_string; //displays foo=ba%20r&amp;baz=bar
echo $query;        //displays foo=ba%20r&baz=bar

$host        = new Host('рф.ru');
$host_string = $formatter->format($host);
echo $host_string; //displays 'xn--p1ai.ru'
echo $host;        //displays 'рф.ru'

$url        = Url::createFromUrl('https://рф.ru:81?foo=ba%20r&baz=bar');
$url_string = $formatter->format($url);
echo $url_string; //displays https://xn--p1ai.ru:81?foo=ba%20r&amp;baz=bar
echo $url;        //displays https://рф.ru:81?foo=ba%20r&baz=bar
~~~