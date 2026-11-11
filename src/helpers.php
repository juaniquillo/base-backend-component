<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent;

use BackedEnum;
use Juaniquillo\BaseBackendComponent\Cache\DefaultCache;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;
use Juaniquillo\BaseBackendComponent\Contracts\ThemeManager;

/**
 * @return DefaultCache<mixed>
 */
function cache(string $name): DefaultCache
{
    /**
     * @var array<string, DefaultCache<mixed>>
     */
    static $cache = [];

    $cache[$name] ??= new DefaultCache();

    return $cache[$name];
}

/**
 * @param array<string, string|array<string|int, string>> $themes
 */
function processThemes(array $themes, ThemeManager $manager): ?string
{
    return $manager->processThemes(themes: $themes);
}

/** @phpstan-assert-if-true BackendComponent $component */
function isComponent(mixed $component): bool
{
    return $component instanceof BackendComponent;
}

/** @phpstan-assert-if-true BackedEnum $enum */
function isBackedEnum(mixed $enum): bool
{
    return $enum instanceof \BackedEnum;
}
