URI manipulations
=======

[![Build Status](https://img.shields.io/travis/thephpleague/uri-manipulations/master.svg?style=flat-square)](https://travis-ci.org/thephpleague/uri-manipulations)
[![Latest Version](https://img.shields.io/github/release/thephpleague/uri-manipulations.svg?style=flat-square)](https://github.com/thephpleague/uri-manipulations/releases)

The `league-uri-manipulations` repository contains:

- an URI formatter to format URI string representation output;
- an URI function to get the URI object reference information according to RFC3986;
- URI middlewares to filter Uri objects;

To be used, the URI objects are required to implement one of the following interface:

- `Psr\Http\Message\UriInteface`;
- `League\Uri\Schemes\Uri`;

System Requirements
-------

You need:

- **PHP >= 5.6.0**  but the latest stable version of PHP is recommended
- `psr\http\message`
- `league-uri-interfaces`
- `league-uri-components`

To use the library.

Documentation
------

All functions and classes are located under the following namespace : `League\Uri\Manipulations`

## URI properties

```php
<?php

function League\Uri\Modifiers\uri_reference(mixed $uri [, mixed $base_uri]): array
```

This function analyzes the submitted URI object and returns an associative array containing information regarding the URI-reference as per [RFC3986](https://tools.ietf.org/html/rfc3986#section-4.1).

### Parameters

- `$uri` implements `Psr\Http\Message\UriInterface` or `League\Uri\Interfaces\Uri`
- `$base_uri` optional, implements `Psr\Http\Message\UriInterface` or `League\Uri\Interfaces\Uri`. Required if you want to detect same document reference.

### Returns Values

An associative array is returned. The following keys are always present within the array and their content is always a boolean:

- `absolute_uri`
- `network_path`
- `absolute_path`
- `relative_path`
- `same_document`

### Example

```php
<?php

use League\Uri\Schemes\Http as HttpUri;
use function League\Uri\Modifiers\uri_reference;

$uri = HttpUri::createFromString("//스타벅스코리아.com/how/are/you?foo=baz");
$alt_uri = HttpUri::createFromString("//xn--oy2b35ckwhba574atvuzkc.com/how/are/you?foo=baz#bar");

var_dump(uri_reference($uri));
//displays something like
// array(5) {
//   'absolute_uri' => bool(false)
//   'network_path' => bool(true)
//   'absolute_path' => bool(false)
//   'relative_path' => bool(false)
//   'same_document' => bool(false)
// }

var_dump(uri_reference($uri, $alt_uri));
//displays something like
// array(5) {
//   'absolute_uri' => bool(false)
//   'network_path' => bool(true)
//   'absolute_path' => bool(false)
//   'relative_path' => bool(false)
//   'same_document' => bool(true)  //can be true only if a base URI is provided
// }
```

## URI Formatting

The Formatter class helps you format your URI according to your output.

```php
<?php

public Formatter::setHostEncoding(int $format): void
public Formatter::setQuerySeparator(string $separator): void
public Formatter::preserveQuery(bool $status): void
public Formatter::preserveFragment(bool $status): void
public Formatter::__invoke(mixed $uri): string
```

This main method `__invoke` expects one of the following argument:

- an Uri object (which implements PSR-7 `UriInterface` or the `League\Interfaces\Uri` interface);
- a `League\Interfaces\Component` Interface.

and returns the URI string representation according to the settings you gave it using the remaining methods. **The returned string MAY no longer be a valid URI**

A host can be output as encoded in ascii or in unicode. By default the formatter encode the host in unicode. To set the encoding you need to specify one of the predefined constant:

- `Formatter::HOST_AS_UNICODE` to set the host encoding to IDN;
- `Formatter::HOST_AS_ASCII`   to set the host encoding to ascii;

### Example

```php
<?php

use League\Uri\Formatter;
use League\Uri\Schemes\Http as HttpUri;

$formatter = new Formatter();
$formatter->setHostEncoding(Formatter::HOST_AS_ASCII);
$formatter->setQuerySeparator('&amp;');
$formatter->preserveFragment(true);

echo $formatter(HttpUri::createFromString('https://рф.ru:81?foo=ba%20r&baz=bar'));
//displays https://xn--p1ai.ru:81?foo=ba%20r&amp;baz=bar#
```

## URI Middlewares

Technically speaking, an URI middleware:

- is a callable. If the URI middleware is a class it must implement PHP’s `__invoke` method.
- expects its single argument to be an URI object which implements either:

    - `Psr\Http\Message\UriInteface`;
    - `League\Uri\Interfaces\Uri`;

- must return a instance of the submitted object.
- must be an immutable value object if it is an object.
- is transparent when dealing with error and exceptions. It must not alter of silence them apart from validating their own parameters.

Here's a the URI middleware signature

```php
<?php

function(Psr\Http\Message\UriInteface $uri): Psr\Http\Message\UriInteface
//or
function(League\Uri\Interfaces\Uri $uri): League\Uri\Interfaces\Uri
```

### Path Middlewares :

All middlewares normalize the URI path component

- `AddLeadingSlash` : add a leading slash to the path
- `RemoveLeadingSlash` : remove the leading slash to the path if it exists
- `AddTrailingSlash` : add a trailing slash to the path
- `RemoveTrailingSlash` : remove the trailing slash to the path if it exists
- `AppendSegment` : append segments to the path
- `PrependSegment` : prepend segments to the path
- `ReplaceSegment` : Replace specified path segments
- `RemoveSegments` : Remove specified path segments
- `FilterSegments` : Filters the path segments
- `Extension` : update the path extension
- `RemoveDotSegments` : Remove the path dot segments according to RFC3986
- `RemoveEmptySegments` : Remove the path empty segments
- `DataUriParameters` : update the paramaters associated to a Data Uri
- `DataUriToAscii` : convert a Data Uri into its ASCII representation
- `DataUriToBinary` : convert a Data Uri into its Binary representation
- `Typecode` : Update the FTP Uri typecode path information

### Host Middlewares:

All middlewares normalize the component

- `AppendLabel` : append labels to the path
- `PrependLabel` : prepend labels to the path
- `ReplaceLabel` : Replace specified host labels
- `RemoveLabels` : Remove specified host labels
- `FilterLabels` : Filters the host labels
- `HostToAscii` : convert the host into its ASCII representation
- `HostToUnicode` : convert the host into its Unicode representation
- `RemoveZoneIdentifier` : Remove the Zone Identifier of an IPv6 host

### Query Middlewares:

All middlewares normalize the component

- `KsortQuery` : Sort a query according to its keys
- `MergeQuery` : Update an Uri querystring
- `RemoveQueryKeys` : Remove specified query pairs according to their keys
- `FilterQuery` : Filters the query pairs

### complete URI Middlewares

- `Normalize` : Normalize an URI to enable URI comparison
- `DecodeUnreservedCharacters` : Used by `Normalize` to better compare URI
- `Resolve` : Resolve an URI against a Base URI according to RFC3986
- `Relativize` : Relativize an URI against a Base URI
- `Pipeline` : Manipulate an Uri Object using a Stack of Uri Middleware

Testing
-------

`League URI Manipulation` has a [PHPUnit](https://phpunit.de) test suite and a coding style compliance test suite using [PHP CS Fixer](http://cs.sensiolabs.org/). To run the tests, run the following command from the project folder.

``` bash
$ composer test
```

Contributing
-------

Contributions are welcome and will be fully credited. Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

Security
-------

If you discover any security related issues, please email nyamsprod@gmail.com instead of using the issue tracker.

Credits
-------

- [ignace nyamagana butera](https://github.com/nyamsprod)
- [All Contributors](https://github.com/thephpleague/uri/contributors)

License
-------

The MIT License (MIT). Please see [License File](LICENSE) for more information.