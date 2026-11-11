# AGENTS.md

- **PHP Version**: `^8.3` (PHP 8.3+ required).
- **Testing**: Run `composer test` (PHPUnit; no Testbench).
- **Coverage**: 100% statement coverage is required. Run `composer test` then `composer run coverage:check` (parses `build/logs/clover.xml`). Only filesystem-error branches may be excluded, always with an explicit `@codeCoverageIgnoreStart`/`End` reason.
- **Static Analysis**: Run `composer analyse` (PHPStan, level 9).
- **Code Style**: Run `composer format` (PHP-CS-Fixer). CI enforces `composer run format:check` (dry-run).
- **Rector**: Run `composer run rector:check` (dry-run); `composer run rector` writes fixes locally.
- **Quality Assurance**: Run `composer qa` to run analysis, formatting check, Rector check, and tests together.
- **Namespaces & Autoloading**: `Juaniquillo\BaseBackendComponent\` maps to `src/`, tests map to `Juaniquillo\BaseBackendComponent\Tests\` under `tests/`. `src/helpers.php` is autoloaded via `files`.

## Design rules

- **Composition over inheritance.** Every concrete class (`BaseBackendComponent`, `DefaultAttributeBag`, `DefaultContentsComponent`, `ComponentDeserializer`, `IndividualComponentDeserializer`, `GenericThemeManager`, `ThemeList`, caches) is `final` and is a *blueprint*. Flavor packages must not extend them; they compose the traits in `Concerns\` into their own classes.
- **All repeatable logic belongs in a trait.** If two consuming packages would copy code, extract it to `Concerns\` first.
- **Traits must stay usable by `readonly` classes** where they represent pure data (a `readonly` class fatals when composing a trait with non-readonly state). Stateless traits taking parameters are preferred (see `MergesAttributeClasses`).
- **Framework-agnostic only.** No `toHtml()`, no renderable interface, no template engine, no framework wiring, no service provider. Rendering and render caching live in the consuming packages.
- **No bundled themes.** `Utils\ThemeList` and `GenericThemeManager` require an explicit path; nothing theme-related ships in this repository.
- **Keep utility classes package-specific.** Do not add helpers that only one consumer needs (cell bags, modal/table builders, enums, factories); those belong to the consuming package.

## Code conventions

- **Never use em dashes** (the U+2014 character) anywhere: code, comments, docblocks, Markdown, commit messages. Rewrite the sentence with commas, colons, semicolons, or parentheses instead.
- Prefix global PHP functions with `\` inside `src/` (e.g., `\is_array()`, `\count()`, `\trim()`).
- Fluent return types (`: static`) on all setters.
- Namespaced helpers in `src/helpers.php`; import with `use function Juaniquillo\BaseBackendComponent\processThemes;`.
- Trait-per-concern pattern for all reusable behavior.
- Never silence PHPStan with `@phpstan-ignore`, baselines, type-widening, or inline `@var` overrides; fix the code instead. Narrowing through a runtime-validated `@phpstan-assert` is allowed when the annotation is backed by checks that throw (see `DeserializesComponents::assertComponentArray()`).
