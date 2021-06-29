# Stackflows Laravel Plugin


## Installation

You can install the package via composer:

```bash
composer require stackflows/laravel-plugin-stackflows
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Stackflows\StackflowsPlugin\StackflowsServiceProvider" --tag="stackflows-config"
```

This is the contents of the published config file:

```php
return [
    /*
     * Address of the Stack Flow Gateway API.
     */
    'host' => env('STACKFLOWS_HOST'),

    /*
     * Stackflows instance UUID.
     */
    'instance' => env('STACKFLOWS_INSTANCE'),
];
```

## Usage

```php
$client = new Stackflows\StackflowsPlugin\Stackflows();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sergey A.](https://github.com/NookeST)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
