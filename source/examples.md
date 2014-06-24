---
layout: layout
title: Examples
---

# Examples

## Parse and modify an URL

A simple example to show you how to manipulate an URL and its component.

~~~.language-php
<?php
use League\Url\Url;

$url = Url::createFromUrl(
    'http://user:pass@www.example.com:81/path/index.php?query=toto+le+heros#top'
);

//let update the Query String
$query = $url->getQuery();
$query->modify(array('query' => "lulu l'allumeuse", "foo" => "bar")); 
$query['sarah'] = "o connors"; //adding a new parameter

$url->setScheme('ftp'); //change the URLs scheme
$url->setFragment(null); //remove the fragment
$url->setPort(21);
$url->getPath()->remove('path/index.php'); //remove part of the path
$url->getPath()->prepend('mongo db'); //prepend the path
echo $url, PHP_EOL; 
// output ftp://user:pass@www.example.com:21/mongo%20db?query=lulu%20l%27allumeuse&foo=bar&sarah=o%20connors
~~~

## Using an Immutable URL to create a pagination

A simple example to show you how to create a pagination will retaining the original uri

~~~.language-php
<?php
use League\Url\UrlImmutable;

//create an URL from the current page
$url = UrlImmutable::createFromServer($_SERVER);
// array to hold the generated URLs
$paginations = array();
//get the current path
$query = $url->getQuery();
foreach (range(1, 5) as $index) {
    $query['page'] = $index;
    //we generate the new Url based on the original $url_immutable object
    $paginations[] = $url->setQuery($query);
}

//$paginations now contains 5 new League\Url\UrlImmutable objects 
//but $url has not change
foreach ($paginations as $uri) {
    $res = $uri instanceof 'League\Url\UrlImmutable'; // $res is true
	$url->sameValueAs($uri); // return false
}
~~~

Learn more about how this all works in the [Overview](/overview).