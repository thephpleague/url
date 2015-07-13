---
layout: default
title: The User Information part
---

# The User Information part

The library provides a `League\Uri\UserInfo` class to ease interacting with the user information URI part.

## Instantiation

### Using the default constructor

The constructor expects 2 optional arguments:

- the user login
- the user password

~~~php
use League\Uri;

$info = new Uri\UserInfo('foo', 'bar');
echo $info; //display 'foo:bar'

$empty_info = new Uri\UserInfo();
echo $empty_info; //display ''

$alt_info = new Uri\UserInfo(new Uri\User('foo'), new Uri\Pass('bar'));
echo $alt_info; //display 'foo:bar'
~~~

### Using a League\Uri\Url object

You can also get a `UserInfo` object from an `League\Uri\Url` class:

~~~php
use League\Uri;

$url = Uri\Uri::createFromComponents(new Uri\Schemes\Registry(), parse_url('http://john:doe@example.com:81/'));
$userInfo = $url->userInfo; //return a League\Uri\UserInfo object
echo $userInfo; // display 'john:doe'
~~~

<p class="message-warning">If the submitted value are not valid user and/or password string an <code>InvalidArgumentException</code> will be thrown.</p>

## User info representations

### String representation

Basic representations is done using the following methods:

~~~php
use League\Uri\UserInfo;

$info = new UserInfo('foo', 'bar');
$info->__toString();      //return 'foo:bar'
$info->getUriComponent(); //return 'foo:bar@'
~~~

### Array representation

The user information can be represented as an array of its internal properties. Through the use of the `UserInfo::toArray` method the class returns the object array representation.

~~~php
use League\Uri\UserInfo;

$info = new UserInfo('foo', 'bar');
$info->toArray();
// returns [
//     'user' => 'foo',
//     'pass' => 'bar',
// ]
~~~

<p class="message-notice">If not user property is set, the <code>toArray</code> method will return an empty <code>null</code> filled array even if the `pass` property is not empty</p>

~~~php
use League\Uri\UserInfo;

$info = new UserInfo('', 'bar');
$info->toArray();
// returns [
//     'user' => null,
//     'pass' => null,
// ]
~~~

## Accessing User information content

To acces the user login and password information you need to call the respective `UserInfo::getUser` and `UserInfo::getPass` methods like shown below.

~~~php
use League\Uri;

$info = new Uri\UserInfo('foo', 'bar');
$info->getUser(); //return 'foo'
$info->getPass(); //return 'bar'

$url = Uri\Schemes\Http::createFromString('http://john:doe@example.com:81/');
$url->userInfo->getUser(); //return 'john'
$url->userInfo->getPass(); //return 'doe'
~~~

To get access to the component classes you can use PHP's magic `__get` method:

~~~php
use League\Uri;

$info = new Uri\UserInfo('foo', 'bar');
$info->user; //return a League\Uri\User class
$info->pass; //return a League\Uri\Pass class

$url = Uri\Schemes\Http::createFromString('http://john:doe@example.com:81/');
$url->userInfo->user->__toString(); //return 'john'
$url->userInfo->pass->__toString(); //return 'doe'
~~~

### User information state

The `UserInfo` part is considered empty if its user property is empty.

~~~php
use League\Uri\UserInfo;

$info = new UserInfo('', 'bar');
$info->isEmpty(); //return true
$info->user->isEmpty(); //return true
$info->pass->isEmpty(); //return false
~~~

## Modifying the user information

<p class="message-notice">Because the <code>UserInfo</code> class does not represent a URI component, it does not include a <code>modify</code> method</p>

<p class="message-notice">If the modifications do not change the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">When a modification fails a <code>InvalidArgumentException</code> is thrown.</p>

To modify the user login and password information you need to call the respective `UserInfo::withUser` and `UserInfo::withPass` methods like shown below.

~~~php
use League\Uri\UserInfo;

$info = new UserInfo('foo', 'bar');
$new_info = $info->withUser('john')->withPass('doe');
echo $new_info; //displays john:doe
echo $info;     //displays foo:bar
~~~
