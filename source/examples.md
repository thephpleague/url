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
$query->modify(array('query' => "lulu l'allumeuse")); 
$query['sarah'] = "o connors"; //adding a new parameter

$url->setScheme('ftp'); //change the URLs scheme
$url->setFragment(null); //remove the fragment
$url->getPath()->remove('path/index.php'); //remove part of the path
$url->getPath()->prepend('mongo db'); //prepend the path
echo $url, PHP_EOL; 
// output ftp://user:pass@www.example.com:81/mongo%20db?query=lulu%20l%27allumeuse&sarah=o%20connors
~~~

## Using an Immutable URL to create a pagination

A simple example to show you how to create a pagination will retaining the original uri

~~~.language-php
<?php
use League\Url\UrlImmutable;

//create an URL from the current page
$url_immutable = UrlImmutable::createFromServer($_SERVER);
//array to hold the generated URLs
$navigation_urls = array();
 //get the current path
$query = $url_immutable->getQuery();
foreach (range(1, 5) as $index) {
    $query['page'] = $index;
    //we generate the new Url based on the original $url_immutable object
    $navigation_urls[$index] = $url_immutable->setQuery($query);
}

//$navigation_urls now contains 5 new UrlImmutable objects 
// but $url_immutable has not change
foreach ($navigation_urls as $index => $uri) {
    echo "Page $index = $uri", PHP_EOL;
}
$url_immutable->sameValueAs($navigation_urls[3]); // return false
~~~

Learn more about how this all works in the [Overview](/overview).