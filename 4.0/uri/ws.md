---
layout: default
title: Websocket URIs
---

# Websockets URI

To work with websockets URI you can use the `League\Uri\Schemes\Ws` class.
This class handles secure and non secure websockets URI.

## Validating a Websocket URI

Websockets URI share the same limitations on validation and manipulation as the [Http](/4.0/uri/http/) object.
Furthermore the websockets URI also share the same standard ports.The main difference between
a `Ws` and the `Http` object is that Websocket URI can not contain any fragment component as per [RFC6455](https://tools.ietf.org/html/rfc6455#section-3).

Attempt to add content to its fragment component will throw an `InvalidArgumentException` exception.

~~~php
use League\Uri\Schemes\Ws as WsUri;

$uri = WsUri::createFromString('wss://thephpleague.com/path/to?here#content');
//throw an InvalidArgumentException
~~~