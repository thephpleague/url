---
layout: default
title: The UserInfo part
---

# The User Information part

The library provides a `League\Url\UserInfo` class to ease User Information manipulation.

## Instantiation

Just like any other component, a new `League\Url\UserInfo` object can be instantiated using [the default constructor](/dev-master/components/overview/#component-instantation). The constructor expects 2 optional arguments. The first argument describes the user login and the latter the user password information.

~~~php
use League\Url\UserInfo;

$info = new UserInfo('foo', 'bar');
echo $scheme; //display 'foo:bar'

$empty_info = new UserInfo();
echo $empty_info; //display ''
~~~

<p class="message-warning">If the submitted value are not valid user and/or password string an <code>InvalidArgumentException</code> will be thrown.</p>

## User info representations

### String representation

Basic representations is done using the following methods:

~~~php
use League\Url\UserInfo;

$info = new UserInfo('foo', 'bar');
$query->__toString();      //return 'foo:bar'
$query->getUriComponent(); //return 'foo:bar@'
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
use League\Url\UserInfo;

$info = new UserInfo('foo', 'bar');
$info->getUser(); //returns a League\Url\User object
$info->getPass(); //returns a League\Url\Pass object
~~~

## Modifying the user information

<p class="message-notice">If the modifications does not change the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">When a modification fails a <code>InvalidArgumentException</code> is thrown.</p>

<p class="message-notice">Unlike other component class, the <code>UserInfo</code> class does not include a <code>withValue</code> method</p>

To modify the user login and password information you need to call the respective <code>UserInfo::withUser</code> and `UserInfo::withPass` methods like shown below.

~~~php
use League\Url\UserInfo;

$info = new UserInfo('foo', 'bar');
$new_info = $info->withUser('john')->getPass('doe');
echo $new_info; //displays john:doe
echo $info;     //displays foo:bar
~~~