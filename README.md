<p align="center">
    <img alt="" src="https://github.com/ShabuShabu/Harness/blob/develop/harness.png"/>
</p>

<h1 align="center">Harness</h1>

<p align="center">
    <a href="https://github.com/ShabuShabu/Harness/actions?query=workflow%3A%22Run+PHPUnit+tests%22">
        <img alt="PHPUnit Tests" src="https://github.com/ShabuShabu/Harness/workflows/Run%20PHPUnit%20tests/badge.svg"/>
    </a>
    <a href="https://github.com/ShabuShabu/Harness/blob/develop/LICENSE.md">
        <img alt="GitHub license" src="https://img.shields.io/github/license/ShabuShabu/Harness">
    </a>
    <a href="https://github.com/ShabuShabu/Harness/tags">
        <img alt="GitHub license" src="https://img.shields.io/github/v/tag/ShabuShabu/Harness.svg?sort=semver">
    </a>
</p>


Opinionated validation abstraction for a [Laravel](https://laravel.com/) [JSON:API](https://jsonapi.org/) that hopefully doesn't get in the way

## ToDo

- Make default messages configurable
- Do not require feedback method
- Live happily ever after

## Installation

You can install the package via composer (:bangbang: at least once it has been published to Packagist...):

```
$ composer require shabushabu/harness
```

### Class Constants

The Harness request expects two class constants, `ROUTE_PARAM` and `JSON_TYPE`, to be present on your models. It's what allows Harness to automatically handle the form request authorization as well as packaging the request data into proper JSON:API format.

```php
class Page extends Model
{
    public const JSON_TYPE = 'pages';
    public const ROUTE_PARAM = 'page';
}
```

[ShabuShabu Abseil](https://github.com/ShabuShabu/Abseil) actually uses those same class constants and is a great match for Harness, so do look into it!

## Usage

Any requests for JSON:API enabled endpoints should extend `ShabuShabu\Harness\Request`.
We can then write our form requests like so:

```php
namespace App\Http\Requests;

use ShabuShabu\Harness\Request;
use function ShabuShabu\Harness\r;

class PageRequest extends Request
{
    public function ruleset(): array
    {
        return [
            'attributes' => [
                'title'        => r()->required()->string(),
                'content'      => r()->required()->string(),
            ],
            'relationships' => [
                'category' => [
                    'type' => r()->string(),
                    'id'   => r()->uuid(),
                ],
            ],
        ];
    }

    public function feedback(): array
    {
        return [
            'attributes' => [
                'title'        => [
                    'required' => 'The title is required',
                    'string'   => 'The title must be a string',
                ],
                'content'      => [
                    'required' => 'The content field is required',
                    'string'   => 'The content field must be a string',
                ],
            ],
            'relationships' => [...]
        ];
    }
}
```

Any request extending `ShabuShabu\Harness\Request` must implement the `ruleset` and `feedback` methods, rather than your usual `rules` and `messages` methods.

Under the hood Harness then uses the `rules` and `messages` methods to weave together a proper request.

Both `ruleset` and `feedback` should return nested arrays and will automatically wrap everything up into a proper JSON:API validation resource like below:

```php
[
    'data.attributes.title' => 'required|string',
    'data.attributes.content' => 'required|string',
    'data.relationships.category.id' => 'uuid',
    'data.relationships.category.type' => 'string',
];
```

Harness ships with all the [validation methods](https://laravel.com/docs/7.x/validation#available-validation-rules) you're used to.
Just camel-case the rule name, use it as a method and hand it any parameters.
Additionally, Harness also adds `latitude` and `longitude` rules.

### Adding rules

In addition to the fluent methods used so far you can also add rules by passing them to the `r` function:

```php
return [
    'attributes' => [
        'title' => r('required', 'string', 'uuid'),
    ],
];
```


If you have any rules that are not covered by Laravel, then you can still add them via the `push` method:

```php
return [
    'attributes' => [
        'title' => r()->push($rule),
    ],
];
```

You can also add them conditionally:

```php
return [
    'attributes' => [
        'title' => r()->push($rule, $condition === true),
    ],
];
```

There are times when you only want to add rules for a given attribute based on a condition. Harness solves this issue like so:

```php
return [
    'attributes' => [
        'title' => r()->string()->when($condition === true),
        'content' => r()->string()->unless($condition === true),
    ],
];
```

### Removing rules

To make a request reusable for various operations, like POST or PUT, Harness allows you to remove single rules, like so: 

```php
return [
    'attributes' => [
        'password' => r()->sometimes()->removeRule('sometimes'),
    ],
];
```

Or only remove them for a given condition:

```php
return [
    'attributes' => [
        'password_1' => r()->sometimes()->removeRuleIf('sometimes', $condition === true),
        'password_2' => r()->sometimes()->removeRuleUnless('sometimes', $condition === true),
    ],
];
```

The above could also be written like so, using some black voodoo magic:

```php
return [
    'attributes' => [
        'password_1' => r()->sometimes()->removeSometimes(),
        'password_2' => r()->sometimes()->removeSometimesIf($condition === true),
        'password_3' => r()->sometimes()->removeSometimesUnless($condition === true),
    ],
];
```

## Testing

```
$ composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email boris@shabushabu.eu instead of using the issue tracker.

## :bangbang: Caveats

Harness is still young and while it is tested, there will probs be bugs. I will try to iron them out as I find them, but until there's a v1 release, expect things to go :boom:.

## Credits

- [All Contributors](../../contributors)
- [BTT](https://boris.travelled.today), aka **Boris Travelled Today**, where Harness was extracted from
- [Ivan Boyko](https://www.iconfinder.com/visualpharm) [[cc]](https://creativecommons.org/licenses/by/3.0/) for the harness icon

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
