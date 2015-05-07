---
layout: default
title: Examples
---

# Simple examples

Let's say you have a document that can be downloaded in different format (CSV, XML, JSON) and you quickly want to generate each format URL. This example illustrates how easy it is to generate theses different URLs from an original URL whithout loosing its information.

~~~php
use League\Url\Url;

$csv_raw_url = "http://download.example.com/path/to/my/file.csv";
$csv_output  = Url::createFromUrl($csv_raw_url);
$xml_output  = $csv_output->withPath($csv_output->getPath()->withExtension('xml'));
$json_output = $csv_output->withPath($csv_output->getPath()->withExtension('json'));

echo $csv_output;  //display "http://download.example.com/path/to/my/file.csv"
echo $xml_output;  //display "http://download.example.com/path/to/my/file.xml"
echo $json_output; //display "http://download.example.com/path/to/my/file.json"
~~~

In this other example we will quickly create pagination URLs suited for HTML output. This example
illustrate how easy and convenient the Formatter class is.

~~~php
use League\Url\Url;
use League\Url\Formatter;

//create a new Formatter object
$formatter = new Formatter();
$formatter->setQuerySeparator('&amp;');
$formatter->setQueryEncoding(PHP_QUERY_RFC3986);
$formatter->setHostEncoding(Formatter::HOST_ASCII);

//create a URL from the current page
$url = Url::createFromServer($_SERVER);
//get the current query component
$query = $url->getQuery();
$links = [];
foreach (range(1, 5) as $pageIndex) {
    //generate a new URL by adding or updating the 'p' parameter of the query component
    $links[$pageIndex] = $formatter->format($url->withQuery($query->merge(['p' => $pageIndex])));
}

//links contains 5 new League\Url\Url objects
foreach ($links as $link) {
    echo $link; //link is a string formatted for HTML output;
}
~~~