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

- **PHP >= 7.0**  but the latest stable version of PHP is recommended
- the `mbstring` extension
- the `intl` extension

Dependencies
-------

- [PSR-7](http://www.php-fig.org/psr/psr-7/)
- [League Uri Interfaces](https://github.com/thephpleague/uri-interfaces)
- [League Uri Components](https://github.com/thephpleague/uri-components)

To use the library.

Installation
------

``` bash
$ composer require league/uri-manipulations
```

Documentation
------

Full documentation can be found at [uri.thephpleague.com](http://uri.thephpleague.com).

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