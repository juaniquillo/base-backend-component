# Base Backend Component

[![CI](https://github.com/juaniquillo/base-backend-component/actions/workflows/ci.yml/badge.svg)](https://github.com/juaniquillo/base-backend-component/actions/workflows/ci.yml)

Framework-agnostic base state for backend components: attributes, contents,
settings, themes, paths and array serialization. You choose how your
components render (templates, plain strings, JSON, or anything else), and
implement that part in your own package.

## Install

```bash
composer require juaniquillo/base-backend-component
```

Requires PHP `^8.3`.

## Use

```php
use Juaniquillo\BaseBackendComponent\BaseBackendComponent;
use Juaniquillo\BaseBackendComponent\Themes\GenericThemeManager;

$component = new BaseBackendComponent(
    'button',
    new GenericThemeManager(__DIR__.'/themes'),
);

$component
    ->setAttribute('id', 'save')
    ->setContent('Save')
    ->setTheme('action', 'success')
    ->setSetting('transition', 'fade');

$array = $component->toArray();
```

Restore later with:

```php
use Juaniquillo\BaseBackendComponent\Deserializers\ComponentDeserializer;

$restored = ComponentDeserializer::fromArray($array);
```

Theme files are plain PHP files returning arrays (default extension `.php`,
configurable through `setThemeExtension()`):

```php
// themes/action.php
return [
    'default' => 'bg-blue-700 hover:bg-blue-800',
    'success' => 'bg-green-700 hover:bg-green-800',
];
```

## Non-goals

This package never renders. There is no `toHtml()`, no renderable interface,
no template engine and no framework integration. Rendering, render caching
and framework wiring belong to the package that consumes this state. The
only cache primitives here are framework-free: the `Cache` contract,
`DefaultCache`, and the PSR-16 `FileCache`.

## Building on it

Every concrete class in this package is `final` and is meant as a blueprint.
Do not extend them. Compose the traits under `Concerns\` into your own
classes and implement only the contracts you need.

See [docs/implementing-a-package.md](docs/implementing-a-package.md) for the
full guide: contract and trait reference, worked examples for components,
attribute bags, contents, themes, serialization, render caching, pitfalls
and a test checklist.

## Develop

```bash
composer install
composer test
composer analyse
composer run format:check
composer run rector:check
composer qa
```

## License

MIT. See [LICENSE.md](LICENSE.md).
