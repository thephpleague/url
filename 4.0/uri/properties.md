---
layout: default
title: Getting URIs informations
---

# Extracting data from URIs

Even thought the package comes bundle with a serie of URI objects representing usual URI, all theses objects expose the same methods.

## Accessing URI parts as and components as strings

You can access the URI individual parts and components as string or integer using their respective getter methods.

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://foo:bar@www.example.com:81/how/are/you?foo=baz#title");
echo $uri->getScheme();    //displays "http"
echo $uri->getUserInfo();  //displays "foo:bar"
echo $uri->getHost();      //displays "www.example.com"
echo $uri->getPort();      //displays 81 as an integer
echo $uri->getAuthority(); //displays "foo:bar@www.example.com:81"
echo $uri->getPath();      //displays "/how/are/you"
echo $uri->getQuery();     //displays "foo=baz"
echo $uri->getFragment();  //displays "title"
~~~

## Accessing URI parts and components as objects

To access a specific URI part or component as an object you can use PHP's magic method `__get` as follow.

~~~php
use League\Uri\Schemes\Ws as WsUri;

$uri = WsUri::createFromString("http://foo:bar@www.example.com:81/how/are/you?foo=baz");
$uri->scheme;   //return a League\Uri\Components\Scheme object
$uri->userInfo; //return a League\Uri\Components\UserInfo object
$uri->host;     //return a League\Uri\Components\Host object
$uri->port;     //return a League\Uri\Components\Port object
$uri->path;     //return a League\Uri\Components\HierarchicalPath object
$uri->query;    //return a League\Uri\Components\Query object
$uri->fragment; //return a League\Uri\Components\Fragment object
~~~

Using this technique you can get even more informations regarding your URI.

~~~php
use League\Uri\Schemes\Http as HttpUri;

$uri = HttpUri::createFromString("http://foo:bar@www.example.com:81/how/are/you?foo=baz");
$uri->host->isIp();           //return false the URI uses a registered hostname
$uri->userInfo->getUser();    //return "foo" the user login information
$uri->fragment->getContent(); //return 'null' because to fragment component is undefined
$uri->path->getBasename();    //return "you"
$uri->query->getValue("foo"); //return "baz"
~~~

<p class="message-notice">The actual methods attach to each component depend on the underlying component object used. For instance a <code>DataUri::path</code> object does not expose the same methods as a <code>Ws::path</code> object would.</p>

To get more informations about component properties refer to the [components documentation](/4.0/components/overview/)
