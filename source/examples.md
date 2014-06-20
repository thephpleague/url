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

$immutable = UrlImmutable::createFromServer($_SERVER); //create an URL from the current page
echo "The original url : " . $immutable. PHP_EOL; // output the current page URL
$query = $immutable->getQuery();
foreach (range(1, 5) as $index) {
    $query['page'] = $index;
    $pages_list[$index] = $immutable->setQuery($query);
}

//page list contains 5 new UrlImmutable object and $immutable has not change
foreach ($pages_list as $index => $uri) {
    echo "Page $index = $uri", PHP_EOL;
}
if (! $immutable->sameValueAs($pages_list[3])) {
	echo "those two object differ!", PHP_EOL; // this will be output
}
~~~

Learn more about how this all works in the [Overview](/overview).