---
layout: default
title: Examples
---

# Examples

## Parse and modify a URL

A simple example to show you how to manipulate a URL and its component:

~~~php
<?php
use League\Url\Url;

$url = Url::createFromUrl(
    'http://user:pass@www.example.com:81/path/index.php?query=toto+le+heros#top'
);

//let's update the Query String
$query = $url->getQuery();
$new_query = $query->mergeWith(['query' => "lulu l'allumeuse", "foo" => "bar"]);

//let's update the path
$path = $url->getPath();
$new_path = $path
		->without('path/index.php')
		->prepend('mongo db');

$new_url = $url
	->withScheme('ftp')
	->withFragment(null)
	->withPort(21)
	->withPath($new_path)
	->withQuery($new_query);

echo $url; // 'http://user:pass@www.example.com:81/path/index.php?query=toto%20le%20heros#top'
echo $new_url; // 'ftp://user:pass@www.example.com/mongo%20db?query=lulu%20l%27allumeuse&foo=bar'
~~~

## Implementing Pagination

A simple example to show you how to implement pagination while retaining the original URI:

~~~php
<?php
use League\Url\UrlImmutable;

//create a URL from the current page
$url = Url::createFromServer($_SERVER);
// array to hold the generated URLs
$paginations = [];
//get the current path
$query = $url->getQuery();
foreach (range(1, 5) as $index) {
    //we generate the new Url based on the original $url_immutable object
    $paginations[] = $url->withQuery($query->mergeWith(['p' => $index]));
}

//$paginations now contains 5 new League\Url\Url objects
//but $url has not change
foreach ($paginations as $uri) {
    $res = $uri instanceof 'League\Url\Url'; // $res is true
	$url->sameValueAs($uri); // return false
}
~~~

Learn more about how this all works in the [Overview](/overview).