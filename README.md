# Google Places API Library

[![Latest Version](https://img.shields.io/github/tag/2amigos/google-places-library.svg?style=flat-square&label=release)](https://github.com/2amigos/google-places-library/tags)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/2amigos/google-places-library/master.svg?style=flat-square)](https://travis-ci.org/2amigos/google-places-library)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/2amigos/google-places-library.svg?style=flat-square)](https://scrutinizer-ci.com/g/2amigos/google-places-library/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/2amigos/google-places-library.svg?style=flat-square)](https://scrutinizer-ci.com/g/2amigos/google-places-library)
[![Total Downloads](https://img.shields.io/packagist/dt/2amigos/google-places-library.svg?style=flat-square)](https://packagist.org/packages/2amigos/google-places-library)

Extension library to interact with [Google Places API](https://developers.google.com/places/documentation/index)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ composer require 2amigos/google-places-library
```

or add

```
"2amigos/google-places-library": "*"
```

to the `require` section of your `composer.json` file.

Usage
-----

Using `SearchClient` class:

```
use Da\Google\Places\Client\SearchClient 

$search = new SearchClient('{YOURGOOGLEAPIKEY}');

// $search->forceJsonArrayResponse(); // if you want to get arrays instead of objects
// $search = new SearchClient('{YOURGOOGLEAPIKEY}', 'xml'); // if you wish to handle XML responses (JSON is highly recommended)


// If you setup the format in 'xml', the returned value will be an array.
// The library will decode the response automatically
var_dump($search->text('restaurants in Inca Mallorca'));

```

Using `PlaceClient` class:

```
use Da\Google\Places\Client\PlaceClient

$place = new PlaceClient('{YOURGOOGLEAPIKEY}');

// $place = new PlaceClient('{YOURGOOGLEAPIKEY}', 'xml'); // if you wish to handle XML responses (JSON is highly recommended)

$place->details('{REFERENCEIDOFPLACE}'));

```

Further Information
-------------------

For further information regarding the multiple parameters of Google Places please visit
[its API reference](https://developers.google.com/places/documentation/index)


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Clean code
 
We have added some development tools for you to contribute to the library with clean code: 

- PHP mess detector: Takes a given PHP source code base and look for several potential problems within that source.
- PHP code sniffer: Tokenizes PHP, JavaScript and CSS files and detects violations of a defined set of coding standards.
- PHP code fixer: Analyzes some PHP source code and tries to fix coding standards issues.

And you should use them in that order. 

### Using php mess detector

Sample with all options available:

```bash 
 ./vendor/bin/phpmd ./src text codesize,unusedcode,naming,design,controversial,cleancode
```

### Using code sniffer
 
```bash 
 ./vendor/bin/phpcs -s --report=source --standard=PSR2 ./src
```

### Using code fixer

We have added a PHP code fixer to standardize our code. It includes Symfony, PSR2 and some contributors rules. 

```bash 
./vendor/bin/php-cs-fixer fix ./src
```

## Testing

 ```bash
 $ ./vendor/bin/phpunit
 ```


## Credits

- [Antonio Ramirez](https://github.com/tonydspaniard)
- [All Contributors](https://github.com/2amigos/google-places-library/graphs/contributors)

## License

The BSD License (BSD). Please see [License File](LICENSE.md) for more information.

<blockquote>
    <a href="http://www.2amigos.us"><img src="http://www.gravatar.com/avatar/55363394d72945ff7ed312556ec041e0.png"></a><br>
    <i>Custom Software Development | Web & Mobile Development Software</i><br> 
    <a href="http://www.2amigos.us">www.2amigos.us</a>
</blockquote>
