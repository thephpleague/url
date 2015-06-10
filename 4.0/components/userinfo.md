---
layout: default
title: The User Information part
---

# The User Information part

The library provides a `League\Url\UserInfo` class to ease interacting with the user information URL part.

## Instantiation

The constructor expects 2 optional arguments. The first argument describes the user login and the latter the user password information.

~~~php
use League\Url;

$info = new Url\UserInfo('foo', 'bar');
echo $scheme; //display 'foo:bar'

$empty_info = new UserInfo();
echo $empty_info; //display ''
~~~

You can also get a `UserInfo` object from an `League\Url\Url` class:

~~~php
use League\Url;

$url = Url\Url::createFromUrl('http://john:doe@example.com:81/');
$userInfo = $url->userInfo; //returns a League\Url\UserInfo() object
echo $userInfo; // display 'john:doe'
~~~

<p class="message-warning">If the submitted value are not valid user and/or password string an <code>InvalidArgumentException</code> will be thrown.</p>

## User info representations

### String representation

Basic representations is done using the following methods:

~~~php
use League\Url\UserInfo;

$info = new UserInfo('foo', 'bar');
$info->__toString();      //return 'foo:bar'
$info->getUriComponent(); //return 'foo:bar@'
~~~

### Array representation

The user information can be represented as an array of its internal properties. Through the use of the `UserInfo::toArray` method the class returns the object array representation.

~~~php
use League\Url\UserInfo;

$info = new UserInfo('foo', 'bar');
$info->toArray();
// returns [
//     'user' => 'foo',
//     'pass'   => 'bar',
// ]
~~~

## Accessing User information content

To acces the user login and password information you need to call the respective `UserInfo::getUser` and `UserInfo::getPass` methods like shown below.

~~~php
use League\Url\Url;
use League\Url\UserInfo;

$info = new UserInfo('foo', 'bar');
$info->getUser(); //returns 'foo'
$info->getPass(); //returns 'bar'

$url = Url\Url::createFromUrl('http://john:doe@example.com:81/');
$url->userInfo->getUser(); //returns 'john'
$url->userInfo->getPass(); //returns 'doe'
~~~

To get access to the component class you can use the magic `__get` method:

~~~php
use League\Url\UserInfo;

$info = new UserInfo('foo', 'bar');
$info->user; //returns a League\Url\Component class
$info->user; //returns a League\Url\Component class

$url = Url\Url::createFromUrl('http://john:doe@example.com:81/');
$url->userInfo->user->__toString(); //returns 'john'
$url->userInfo->pass->__toString(); //returns 'doe'
~~~

### User information state

Unlike other URL part, the `UserInfo` part is considered empty if its user property is empty.

~~~php
use League\Url\UserInfo;

$info = new UserInfo('', 'bar');
$info->isEmpty(); //returns true
$info->user->isEmpty(); //return true
$info->pass->isEmpty(); //return true
~~~

<p class="message-warning">The password information is automatically removed when the user information is empty</p>


## Modifying the user information

<p class="message-notice">If the modifications does not change the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">When a modification fails a <code>InvalidArgumentException</code> is thrown.</p>

<p class="message-notice">Unlike other component class, the <code>UserInfo</code> class does not include a <code>modify</code> method</p>

To modify the user login and password information you need to call the respective <code>UserInfo::withUser</code> and `UserInfo::withPass` methods like shown below.

~~~php
use League\Url\UserInfo;

$info = new UserInfo('foo', 'bar');
$new_info = $info->withUser('john')->getPass('doe');
echo $new_info; //displays john:doe
echo $info;     //displays foo:bar
~~~
