URI manipulations
=======

[![Build Status](https://img.shields.io/travis/thephpleague/uri-manipulations/master.svg?style=flat-square)](https://travis-ci.org/thephpleague/uri-manipulations)
[![Latest Version](https://img.shields.io/github/release/thephpleague/uri-manipulations.svg?style=flat-square)](https://github.com/thephpleague/uri-manipulations/releases)

The package contains:

- an object to format URI string representation output;
- a function to get the URI reference information according to [RFC3986](https://tools.ietf.org/html/rfc3986#section-4);
- URI middlewares to ease filtering and manipulating Uri objects;

System Requirements
-------

You need:

- **PHP >= 7.0**  but the latest stable version of PHP is recommended
- the `intl` extension

Dependencies
-------

- [PSR-7](http://www.php-fig.org/psr/psr-7/)
- [League Uri Interfaces](https://github.com/thephpleague/uri-interfaces)
- [League Uri Components](https://github.com/thephpleague/uri-components)


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

`League Uri Manipulations` has a :

- a [PHPUnit](https://phpunit.de) test suite
- a coding style compliance test suite using [PHP CS Fixer](http://cs.sensiolabs.org/).
- a code analysis compliance test suite using [PHPStan](https://github.com/phpstan/phpstan).

To run the tests, run the following command from the project folder.

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