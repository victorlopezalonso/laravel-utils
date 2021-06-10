# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/victorlopezalonso/laravel-utils.svg?style=flat-square)](https://packagist.org/packages/victorlopezalonso/laravel-utils)
[![Build Status](https://img.shields.io/travis/victorlopezalonso/laravel-utils/master.svg?style=flat-square)](https://travis-ci.org/victorlopezalonso/laravel-utils)
[![Quality Score](https://img.shields.io/scrutinizer/g/victorlopezalonso/laravel-utils.svg?style=flat-square)](https://scrutinizer-ci.com/g/victorlopezalonso/laravel-utils)
[![Total Downloads](https://img.shields.io/packagist/dt/victorlopezalonso/laravel-utils.svg?style=flat-square)](https://packagist.org/packages/victorlopezalonso/laravel-utils)

This package gives you some helper classes and super charged boiler plate for new Laravel projects. We made this package to aim API Rest services for mobile apps plus a dashboard with standard functionality.

## Installation

You can install the package via composer:

```bash
composer require victorlopezalonso/laravel-utils
```

### **Classes**

### - Copy


This class allows you to share translations for your project separated into server, app and dashboard translations. 

Using this class you can serve a list of translations with your app via JSON and use versioning to change the texts that your app is using without the need to reupload the app to the market place.

``` php
if(!$user) {
    return Copy.server('USER_NOT_FOUND');
}
```


## Usage

``` php
asdfasdf
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email victorlopezalonso@gmail.com instead of using the issue tracker.

## Credits

- [Víctor López Alonso](https://github.com/victorlopezalonso)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
