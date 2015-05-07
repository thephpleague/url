---
layout: default
title: Examples
---

# Simple examples

Let's say you have a document that can be downloaded in different format (CSV, XML, JSON) and you quickly want to generate each format URL. This example illustrates how easy it is to generate theses different URLs from an original URL whithout loosing its information.

~~~php
use League\Url\Url;

$csv_output  = Url::createFromUrl("http://download.example.com/path/to/my/file.csv");
$xml_output  = $csv_output->withPath($csv_output->getPath()->withExtension('xml'));
$json_output = $csv_output->withPath($csv_output->getPath()->withExtension('json'));

echo $csv_output;  //display "http://download.example.com/path/to/my/file.csv"
echo $xml_output;  //display "http://download.example.com/path/to/my/file.xml"
echo $json_output; //display "http://download.example.com/path/to/my/file.json"
~~~
