---
layout: default
title: Examples
---

# Simple example

Let's say you have a document that can be downloaded in different format (CSV, XML, JSON) and you quickly want to generate each format URI. This example illustrates how easy it is to generate theses different URIs from an original URI.

~~~php
use League\Uri\Modifiers\AppendSegments;
use League\Uri\Modifiers\Extension;
use League\Uri\Modifiers\ReplaceLabels;
use League\Uri\Pipeline;
use League\Uri\Schemes\Http as HttpUri;

//let's create the original URI
$uri = HttpUri::createFromString("http://www.example.com/report");

//using the Pipeline class we register and apply the common transformations
$modifiers = (new Pipeline())
	->pipe(new AppendSegments('/purchases/summary'))
	->pipe(new ReplaceLabels(3, 'download'));
$tmpUri = $modifiers->process($uri->withScheme('https'));

//the specific transformation are applied here 
$extension_list = ['csv', 'json', 'xml'];
$links = [];
foreach ($extension_list as $extension) {
    $links[$extension] = (new Extension($extension))->__invoke($tmpUri);
}

// $links is an array of League\Uri\Schemes\Http objects
echo $uri;           // display "http://www.example.com/report"
echo $links['csv'];  // display "https://download.example.com/report/purchases/summary.csv"
echo $links['xml'];  // display "https://download.example.com/report/purchases/summary.xml"
echo $links['json']; // display "https://download.example.com/report/purchases/summary.json"
~~~
