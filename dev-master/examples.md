---
layout: default
title: Examples
---

# Simple example

Let's say you have a document that can be downloaded in different format (CSV, XML, JSON) and you quickly want to generate each format URL. This example illustrates how easy it is to generate theses different URLs from an original URL.

~~~php
use League\Url\Services\Builder as UrlBuilder;

$urlBuilder  = new UrlBuilder();
$extension_list = ['csv', 'json', 'xml'];
$output_links = [];
foreach ($extension_list as $extension) {
    $output_links[$extension] = $urlBuilder
      ->setUrl("http://www.example.com/report")
      ->appendPath("/purchases/summary")
      ->withExtension($extension)
      ->replaceLabel('download', 0)
      ->getUrl();
}

echo $output_links['csv'];  //display "http://download.example.com/report/purchases/summary.csv"
echo $output_links['xml'];  //display "http://download.example.com/report/purchases/summary.xml"
echo $output_links['json']; //display "http://download.example.com/report/purchases/summary.json"
~~~
