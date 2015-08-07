---
layout: default
title: Websocket URIs
---

# Websockets URI

To work with websockets URI you can use the `League\Uri\Schemes\Ws` class.
This class handles secure and non secure websockets URI.

## Validating a Websocket URI

Websockets URI share many aspects with the [Http](/4.0/uri/http/) object:

- limitations on validation;
- All the manipulation methods except for the `Http::resolve`;
- the same standard ports;

The main difference between the `Ws` and the `Http` object is that Websocket URI can not contain any fragment component as per [RFC6455](https://tools.ietf.org/html/rfc6455#section-3).
Of course the `Ws` is not `PSR-7` compliant.

<p class="message-notice">Adding contents to the fragment component throws an <code>RuntimeException</code> exception</p>

~~~php
use League\Uri\Schemes\Ws as WsUri;

$uri = WsUri::createFromString('wss://thephpleague.com/path/to?here#content');
//throw an InvalidArgumentException
~~~