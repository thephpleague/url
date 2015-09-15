---
layout: default
title: Examples
---

# Simple examples

## Parsing an URI

Appart from being able to get all the URI component string using their respective getter method. the URI object also exposed all component as object throught PHP's magic `__get` method. You can use this ability to get even more informations about the URI objects. 

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://url.thephpleague.com/.././report/");
echo $uri->getPath(); //display "/.././report/"
$normalizedPath = $uri->path
	->withoutLeading()
	->withoutTrailingSlash()
	->withoutDotSegments();
echo $normalizedPath; //display "report"

var_dump($uri->host->toArray());
// display
// array(
//	   0 => 'com',
//	   1 => 'thephpleague',
//	   2 => 'url',
//);

echo $uri->getHost(); //display "url.thephpleague.com"
echp $uri->host->getLabel(2); //display "url"
echo $uri->host->getPublicSuffix(); //return com
echo $uri->host->getRegisterableDomain(); //display 'thephpleague.com'
echo $uri->host->getSubDomain(); //display 'url'
~~~

## Using Uri Modifiers

Let's say you have a document that can be downloaded in different format (CSV, XML, JSON) and you quickly want to generate each format URI. This example illustrates how easy it is to generate theses different URIs from an original URI.

~~~php
use League\Uri\Modifiers\AppendSegments;
use League\Uri\Modifiers\Extension;
use League\Uri\Modifiers\Pipeline;
use League\Uri\Modifiers\ReplaceLabels;
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
