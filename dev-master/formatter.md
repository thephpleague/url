---
layout: default
title: The URL Formatter
---

# The Formatter

The `League\Url\Util\Formatter`utility class helps you output a better formatted URL and/or component to easily use them in output format like HTML.

## Formatter Properties

### Host encoding strategy

A host can be output as encoded in ascii or in unicode. By default the formatter encode the host in unicode. To set the encoding you need to specify one of the predefined constant:

- `Formatter::HOST_UNICODE` to set the host encoding to unicode;
- `Formatter::HOST_ASCII`   to set the host encoding to ascii;

~~~php

use League\Url\Util\Formatter;

$formatter = new Formatter();
$formatter->setHostEncoding(Formatter::HOST_ASCII);
echo $formatter->getHostEncoding(); //display the value of Formatter::HOST_ASCII
~~~

### Query encoding strategy

A League\Url\Query object is by default encoded by following RFC 3986. If you need to change this encoding to the old RFC 1738, you just need to update the query encoding as shown below using the following predefined constant:

- `Formatter::QUERY_RFC_3986` an a alias of `PHP_RFC_3986` to set the query encoding to unicode;
- `Formatter::QUERY_RFC_1738` an a alias of `PHP_RFC_3986` to set the query encoding to ascii;

~~~php

use League\Url\Util\Formatter;

$formatter = new Formatter();
$formatter->setQueryEncoding(Formatter::QUERY_RFC_3986);
echo $formatter->getQueryEncoding(); //display the value of Formatter::QUERY_RFC_3986;
~~~

### Modifying the query separator

~~~php

use League\Url\Util\Formatter;

$formatter = new Formatter();
$formatter->setQuerySeparator('&amp;');
echo $formatter->getQuerySeparator(); //returns &amp;
~~~

## Applying the settings to your League\Url package.

### Formatter::format($obj)

The `$obj` parameter can be:

- a `League\Url\Url` object;
- a `League\Url\Host` object;
- a `League\Url\Query` object;

The given parameter is never alter, the `format` method recreate the output by applying the modification you expect.

## Concrete example

~~~php

use League\Url\Host;
use League\Url\Query;
use League\Url\Url;
use League\Url\Util\Formatter;

$formatter = new Formatter();
$formatter->setHostEncoding(Formatter::HOST_ASCII);
$formatter->setQueryEncoding(Formatter::QUERY_RFC_3986);
$formatter->setQuerySeparator('&amp;');

$query = Query::createFromArray(['foo' => 'ba r', "baz" => "bar"]);

$query_string = $formatter->format($query);
echo $query_string; //return foo=ba%20r&amp;baz=bar
echo $query; //returns foo=ba%20r&baz=bar

$host = new Host('рф.ru');
$host_string = $formatter->format($host);
echo $host_String = 'xn--p1ai.ru'
echo $host = 'рф.ru';


$url = Url::createFromUrl('https://рф.ru:81?foo=ba%20r&baz=bar');
$url_string = $formatter->format($url);
echo $url_string; //display https://xn--p1ai.ru:81?foo=ba%20r&amp;baz=bar
echo $url; //display https://рф.ru:81?foo=ba%20r&baz=bar
~~~