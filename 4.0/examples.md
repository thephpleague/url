---
layout: default
title: Examples
---

# Simple example

Let's say you have a document that can be downloaded in different format (CSV, XML, JSON) and you quickly want to generate each format URI. This example illustrates how easy it is to generate theses different URIs from an original URI.

~~~php
use League\Uri\Schemes\Http;

$url = Http::createFromString("http://www.example.com/report");
$extension_list = ['csv', 'json', 'xml'];
$links = [];
foreach ($extension_list as $extension) {
    $links[$extension] = $url
      ->appendPath("/purchases/summary")
      ->withExtension($extension)
      ->replaceLabel(0, 'download')
      ->withScheme('https');
}

// $links is an array of League\Uri\Url objects

echo $url;           // display "http://www.example.com/report"
echo $links['csv'];  // display "https://download.example.com/report/purchases/summary.csv"
echo $links['xml'];  // display "https://download.example.com/report/purchases/summary.xml"
echo $links['json']; // display "https://download.example.com/report/purchases/summary.json"
~~~
