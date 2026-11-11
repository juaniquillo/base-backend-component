# Implementing a package on top of Base Backend Component

This guide is for package authors who want to add their own rendering on top
of the state model provided here. It assumes no particular framework and no
particular output format.

The short version: **compose the traits, implement the contracts, and write
the rendering yourself.**

## Ground rules

1. **Do not extend the example classes.** `BaseBackendComponent`,
   `DefaultAttributeBag`, `DefaultContentsComponent`, `ComponentDeserializer`,
   `IndividualComponentDeserializer`, `GenericThemeManager`, `ThemeList` and
   the cache classes are `final` on purpose. They are blueprints. Extending
   them forces your package to inherit concerns it does not use (props,
   paths, theme compilation, settings, ...).
2. **Build your own classes by composing traits.** Every reusable behavior
   lives in `Juaniquillo\BaseBackendComponent\Concerns\`. If your package
   ends up copying logic, extract it to a trait in your own package first.
3. **Your package owns rendering.** There is no render contract here. Name
   your method whatever fits your output (`toHtml()`, `toMarkup()`,
   `toJson()`, `compile()`, ...) and keep it out of the shared contracts.
4. **Data-only classes should be `readonly` where possible.** Note that a
   `readonly` class cannot compose a trait that declares non-readonly
   properties. Traits intended for value objects must stay stateless and
   accept their inputs as method arguments (`MergesAttributeClasses` is the
   reference example).

## Step 1: choose the contracts

Implement only what your component actually does.

| Contract | Meaning |
|---|---|
| `BackendComponent` | minimum: attributes, attribute bag, `toArray()` |
| `ContentComponent` | holds child content |
| `ContentsComponent` | value object that wraps a contents collection |
| `CompoundComponent` | aggregate of `BackendComponent`, `ContentComponent`, `PathComponent`, `SettingsComponent`, `ThemeComponent` |
| `PathComponent` | name plus optional path segment |
| `SettingsComponent` | scalar settings map |
| `ThemeComponent` | theme selection and compilation |
| `PropsComponent` | rich, non-scalar props (arrays, objects, ...) |
| `AttributeBag` | the shape passed to your renderer |

Skip `CompoundComponent` unless you really provide every concern in it. A
component that only holds content needs `BackendComponent` plus
`ContentComponent`.

## Step 2: pick the traits

| Trait | Provides | Requires |
|---|---|---|
| `IsBackendComponent` | `setAttribute()`, `setAttributes()`, `getAttribute()`, `getAttributes()` | nothing |
| `HasContent` | `setContent()`, `setContents()`, `prependContent()`, `unsetContent()`, `getContents()`, `processContent()` | nothing |
| `HasSettings` | `setSetting()`, `setSettings()`, `getSetting()`, `getSettings()`, `unsetSetting()` | nothing |
| `HasProps` | `setProp()`, `setProps()`, `getProp()`, `getProps()` | nothing |
| `HasPath` | `getName()`, `getPath()`, `getPathOnly()`, `setPath()` | a `$name` property |
| `IsThemeable` | `setTheme()`, `setThemes()`, `getTheme()`, `setThemeManager()`, `compileTheme()` | a `ThemeManager` |
| `MergesAttributeClasses` | folds compiled theme classes into the `class` attribute | nothing (stateless) |
| `SerializesContents` | `toArray()` for a contents collection | `getContents()` |
| `IsThemeManager` | theme file lookup, variant resolution, in-memory cache | a default path |
| `DeserializesComponents` | `fromArray()` / `initComponent()` tree restoration | an `initComponent()` implementation |

Typical combinations:

- element with children: `IsBackendComponent` + `HasContent`
- element with children and themes: add `IsThemeable`
- leaf element: `IsBackendComponent` only
- component that restores from arrays: add `DeserializesComponents` on your
  deserializer, not on the component

## Step 3: write the component

```php
namespace Acme\Widgets;

use Juaniquillo\BaseBackendComponent\Concerns\HasContent;
use Juaniquillo\BaseBackendComponent\Concerns\IsBackendComponent;
use Juaniquillo\BaseBackendComponent\Contracts\AttributeBag;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;
use Juaniquillo\BaseBackendComponent\Contracts\ContentComponent;

final class WidgetComponent implements BackendComponent, ContentComponent
{
    use HasContent;
    use IsBackendComponent;

    public function __construct(
        private readonly string $element,
    ) {
    }

    public function getName(): string
    {
        return $this->element;
    }

    public function getAttributeBag(): AttributeBag
    {
        return new WidgetAttributeBag($this->getAttributes());
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'component' => self::class,
            'attributes' => $this->getAttributes(),
            'contents' => $this->processContent()->toArray(),
        ];
    }

    public function render(): string
    {
        // Your rendering, your rules.
    }
}
```

Rules of thumb:

- Keep `toArray()` symmetric with your deserializer. Anything you emit must
  be consumable again.
- Store `'component' => self::class` so the deserializer knows what to build.
- `processContent()->toArray()` gives you the nested tree for free.

## Step 4: write the attribute bag

The bag is the single object your renderer receives. Compose
`MergesAttributeClasses` so theme classes land in `class` without extra code.

```php
namespace Acme\Widgets;

use Juaniquillo\BaseBackendComponent\Concerns\MergesAttributeClasses;
use Juaniquillo\BaseBackendComponent\Contracts\AttributeBag;

final readonly class WidgetAttributeBag implements AttributeBag
{
    use MergesAttributeClasses;

    /**
     * @param  array<string, int|string|null>  $attributes
     * @param  array<string, mixed>  $extra
     */
    public function __construct(
        private array $attributes,
        private ?string $themes = null,
        private array $extra = [],
    ) {
    }

    /**
     * @return array<string, int|string|null>
     */
    public function getAttributes(): array
    {
        return $this->mergeThemeClasses($this->attributes, $this->themes);
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributesAndProps(): array
    {
        return array_merge($this->getAttributes(), $this->extra);
    }
}
```

Add only the fields your renderer needs. There is no requirement to mirror
`DefaultAttributeBag`, and its integration-specific fields do not exist here.

## Step 5: write the contents value object

```php
namespace Acme\Widgets;

use Juaniquillo\BaseBackendComponent\Concerns\SerializesContents;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;
use Juaniquillo\BaseBackendComponent\Contracts\CompoundComponent;
use Juaniquillo\BaseBackendComponent\Contracts\ContentsComponent;

final readonly class WidgetContents implements ContentsComponent
{
    use SerializesContents;

    /**
     * @param  array<string|int, int|string|CompoundComponent|BackendComponent>  $contents
     */
    public function __construct(
        private array $contents,
    ) {
    }

    /**
     * @return array<string|int, int|string|CompoundComponent|BackendComponent>
     */
    public function getContents(): array
    {
        return $this->contents;
    }
}
```

`SerializesContents` supplies `toArray()`. You only supply `getContents()`.

## Step 6: rendering

Rendering is entirely yours. Two common shapes:

- **String output**: walk the tree and build a string. Accept any
  `BackendComponent` child and recurse.
- **Template output**: hand the attribute bag to your template engine.

Whatever you choose, keep the base contracts free of rendering methods. That
is what allows the same state model to serve radically different outputs.

If you cache rendered output, do it in your package:

```php
use Juaniquillo\BaseBackendComponent\Cache\FileCache;

$cache = new FileCache('/path/to/cache');
$key = md5(json_encode($component->toArray(), JSON_THROW_ON_ERROR));

$html = $cache->get($key) ?? $component->render();

$cache->set($key, $html);
```

`DefaultCache` is an in-memory implementation; `FileCache` is PSR-16 and
usable with any PSR-16 consumer. Neither depends on a framework.

## Step 7: serialization and restoration

JSON-safe export/import is split in two:

- `toArray()` on the component: you write it, keeping it symmetric.
- `fromArray()` on a deserializer: compose `DeserializesComponents`.

```php
namespace Acme\Widgets;

use Juaniquillo\BaseBackendComponent\Concerns\DeserializesComponents;

final class WidgetDeserializer
{
    use DeserializesComponents;
}
```

`DeserializesComponents::initComponent()` does `new $class($name)`. If your
constructor signature differs (enum instead of string, extra arguments),
override that one method:

```php
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;
use Juaniquillo\BaseBackendComponent\Contracts\CompoundComponent;

public function initComponent(array $componentArray): BackendComponent|CompoundComponent
{
    /** @var class-string<BackendComponent|CompoundComponent> $class */
    $class = $componentArray['component'];

    return new $class($componentArray['name']);
}
```

Content keys are preserved during restoration. Keep working with string and
integer keys in `toArray()` output; the deserializer restores them as-is.

Restoration also rebuilds theme managers from the exported `theme.manager`
class name and `theme.path`, so those must be constructible without
arguments.

## Step 8: themes

Theme files are plain PHP files that return arrays. No template engine is
involved, which is why they work in any package:

```php
// themes/action.php
return [
    'default' => 'bg-blue-700 hover:bg-blue-800',
    'success' => 'bg-green-700 hover:bg-green-800',
];
```

Options, from least to most custom:

1. **Use `GenericThemeManager`.** It takes the theme directory in its
   constructor:

   ```php
   use Juaniquillo\BaseBackendComponent\Themes\GenericThemeManager;

   $manager = new GenericThemeManager(__DIR__.'/themes');
   ```

2. **Write your own manager.** Compose `IsThemeManager` and set a default
   path. Useful when your package ships its own theme directory or needs a
   different file extension:

   ```php
   use Juaniquillo\BaseBackendComponent\Concerns\IsThemeManager;
   use Juaniquillo\BaseBackendComponent\Contracts\ThemeManager;

   final class WidgetThemeManager implements ThemeManager
   {
       use IsThemeManager;

       public function __construct()
       {
           $this->setDefaultPath(__DIR__.'/../themes');
       }
   }
   ```

Notes:

- The default theme extension is `.php`. Change it with
  `setThemeExtension('.blade.php')` or any other suffix your loader expects.
- No theme files ship with the base package. Your package provides them.
- A theme directory is required. `GenericThemeManager` defaults to the system
  temp directory so `toArray()` never fails, but `processThemes()` needs a
  real directory with real files.
- `ThemeList::make($directory)->scanFiles()` returns every theme file with its
  name, path and contents, for documentation tools or debugging.

## Step 9: test the seams

Recommended coverage for a package built on this base:

- attributes set, replaced, read back
- content append, keyed content, prepend, unset, nested components
- `toArray()` shape, including nested contents
- round trip: `toArray()` then `fromArray()` produces an equal tree
- custom `initComponent()` path, if you have one
- theme compilation for a single variant and an array of variants
- missing theme file and missing variant both throw
- your renderer's output for a leaf component and for a nested tree

Plain PHPUnit is enough. The base package itself uses no Testbench or
framework bootstrapping.

## Pitfalls

- **`readonly` plus stateful traits**: a `readonly` class cannot use a trait
  with non-readonly properties. Keep value-object traits stateless.
- **Asymmetric serialization**: if `toArray()` emits a key your
  `initComponent()` cannot consume, round trips break. Export only what you
  restore.
- **Theme manager constructors**: restoration instantiates the exported
  manager class with no arguments. Give it a default path.
- **Content keys**: keyed content survives restoration only because it is
  restored with `overwrite: true`. Do not reindex arrays before exporting.
- **Rendering methods in contracts**: adding `toHtml()`-like methods to shared
  contracts locks every consumer into one output format.
- **Logic in concrete classes**: if you are about to copy a method between
  two components, put it in a trait instead.

## Checklist

- [ ] Contracts chosen; nothing implemented that the package does not use
- [ ] Traits composed, no class extended
- [ ] `toArray()` symmetric with the deserializer
- [ ] Attribute bag is your own, using `MergesAttributeClasses`
- [ ] Contents value object uses `SerializesContents`
- [ ] Rendering lives in your package only
- [ ] Themes have a directory and a manager with a default path
- [ ] Round-trip test passing
- [ ] No em dashes in code, comments, or docs
