---
layout: default
title: Examples
---

# Simple example

Let's say you have a document that can be downloaded in different format (CSV, XML, JSON) and you quickly want to generate each format URL. This example illustrates how easy it is to generate theses different URLs from an original URL whithout losing its information.

~~~php
use League\Url\Url;

$csv_raw_url  = "http://download.example.com/path/to/my/file.csv";
$csv_output   = Url::createFromUrl($csv_raw_url);
$current_path = $csv_output->getPart('path');
$xml_output   = $csv_output->withPath($current_path->withExtension('xml'));
$json_output  = $csv_output->withPath($current_path->withExtension('json'));

echo $csv_output;  //display "http://download.example.com/path/to/my/file.csv"
echo $xml_output;  //display "http://download.example.com/path/to/my/file.xml"
echo $json_output; //display "http://download.example.com/path/to/my/file.json"
~~~
