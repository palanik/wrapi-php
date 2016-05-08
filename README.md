wrapi
=====
Wrapper for calling Restful API

*`wrapi`* allows you to call HTTP based APIs just like making calls to ordinary php functions.

[![Latest Stable Version](https://poser.pugx.org/palanik/wrapi/v/stable.svg)](https://packagist.org/packages/palanik/wrapi)
[![Build Status](https://travis-ci.org/palanik/wrapi-php.svg)](https://travis-ci.org/palanik/wrapi-php)
[![License](https://poser.pugx.org/palanik/wrapi/license.svg)](https://github.com/palanik/wrapi-php/blob/master/LICENSE)

## Installation

#### Install with [Composer](https://packagist.org/packages/palanik/wrapi) ####
1. Update your `composer.json` to require `palanik/wrapi` package.
2. Run `composer install` to add wrapi your vendor folder.
```json
{
  "require": {
    "palanik/wrapi": "*"
  }
}
```

or simply run 
```shell
composer require palanik/wrapi
```

## Easy Start

### Approach `I`
1. Create an [array](#endpoints-array) listing all the API endpoints you want to work with.
2. [Wrap](#wrap-endpoints) endpoints with *`wrapi`*.
3. Call individual endpoints as [functions](#make-the-call).

See [Example Code](examples/github/example1.php)

### Approach `II`
1. Create [client object](#client-object) with API Base URL.
2. [Register](#register) API endpoints.
3. Call individual endpoints as [functions](#make-the-call).

See [Example Code](examples/github/example2.php)

------

### Endpoints Array
Declare each endpoint as per the following specifications.

```php
"function_name" => array(
	"method" => "HTTP_METHOD",					// 'GET', 'POST', 'PUT', 'PATCH' or 'DELETE'
	"path" => "relative/path/to/:api/endpoint"	// Can use `Slim`/`express` style path params
)
```

eg. a small set of github.com API
```php
array(
	"repo" => array(
		"method" => "GET",
		"path" => "repos/:owner/:repo"
	),

	"contributors" => array(
		"method" => "GET",
		"path" => "repos/:owner/:repo/contributors"
	),

	"languages" => array(
		"method" => "GET",
		"path" => "repos/:owner/:repo/languages"
	),

	"tags" => array(
		"method" => "GET",
		"path" => "repos/:owner/:repo/tags"
	),

	"branches" => array(
		"method" => "GET",
		"path" => "repos/:owner/:repo/branches"
	)
)
```

### Wrap endpoints
Create a API client object from *`wrapi`*. Provide the base url for the API and the endpoints array.
*`wrapi`* will create a client object with all the necessary functions.

```php
$endpoints = array(...);

$client = new wrapi\wrapi('https://api.github.com/',	// base url for the API
  endpoints 										// your endpoints array
);

// client object contains functions to call the API
```

### Register
Register additional API endpoints with the client object with a function name.

```php
$client("zen", 
	array(
		"method" => "GET",
		"path" => "zen"
		)
	);
```

### Make the call
Call the functions with arguments.

```php
// This will make GET request to 'https://api.github.com/repos/guzzle/guzzle/contributors'
$contributors = $client->contributors('guzzle', 'guzzle');

$zenQuote = $client->zen();
echo "Today's quote: ". $zenQuote;


```

## API

*`wrapi`* is an open ended framework and is not restricted any one or a set of public APIs. All APIs providing HTTP interface to access the endpoints can be wrapped by *`wrapi`* so that you can quickly build your client application.

### Endpoint definition

`method` & `path`/`url` are required.

* `method` - Any one of the HTTP [methods](https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html)
* `path` - route path to API Endpoint. Supports `express` style [path params](http://expressjs.com/en/4x/api.html#req.params)
* `query` - an associative array with name-value pairs. This is optional. Useful where resources are identified via query string parameters
* `url` - fully qualified uri string to override. Useful when api calls connect to a different endpoints

### Client object

The *`wrapi`* object conveniently provides the client interface to the API. Create it by calling `new` *`wrapi\wrapi()`*.

The constructor takes the following arguments:

1. `baseURL` - The base url for the API. eg. `https://api.github.com/repos/guzzle/guzzle/contributors`
2. `endpoints` - The array listing the endpoints of the API. Provide an empty array or a partial list and register endpoints later.
3. `options` - Optional parameter. *`wrapi`* uses [Guzzle](http://docs.guzzlephp.org/) module to connect to API server. The `options` parameter is the same [`options`](http://docs.guzzlephp.org/en/latest/request-options.html) parameter used in `Guzzle``request`.

### Register function

Add endpoints to client object.
```php
$client(function_name, endpoint_definition)
```

1. `function_name` - Alias for the endpoint, also the name of the function to call.
2. `endpoint_definition` - Array defining the endpoint.


### Function calls

Call the API via the function in the client object.  Arguments to the function depend on the API declaration in the endpoints array. 

Provide the arguments in the following order:

1. named `params` in the url path of the endpoint. eg. `$client->contributors('guzzle', 'guzzle')   // guzzle (owner) & guzzle (repo) are path params`
2. `querystring` as an associative array with name-value pairs. eg. `$client->contributors(array("since" => 364)  // querystring ?since=364`
3. `body` - JSON content for  `POST` or `PUT` methods. Skip this argument if not required. 

## Examples

  In examples [folder](examples).

## License

  [MIT](LICENSE)
