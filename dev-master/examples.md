---
layout: default
title: Examples
---

# Simple example

Let's say you have a document that can be downloaded in different format (CSV, XML, JSON) and you quickly want to generate each format URL. This example illustrates how easy it is to generate theses different URLs from an original URL.

~~~php
use League\Url\Url;

$url = Url::createFromUrl("http://www.example.com/report");
$extension_list = ['csv', 'json', 'xml'];
$links = [];
foreach ($extension_list as $extension) {
    $links[$extension] = $url
      ->appendSegments("/purchases/summary")
      ->withExtension($extension)
      ->replaceLabel(0, 'download')
      ->withScheme('ftp');
      //returns a PSR-7 UriInterface compatible object
}

echo $url;           //display "http://www.example.com/report"
echo $links['csv'];  //display "ftp://download.example.com/report/purchases/summary.csv"
echo $links['xml'];  //display "ftp://download.example.com/report/purchases/summary.xml"
echo $links['json']; //display "ftp://download.example.com/report/purchases/summary.json"
~~~
