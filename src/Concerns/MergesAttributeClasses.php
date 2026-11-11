<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Concerns;

/**
 * Merges compiled theme classes into the `class` attribute.
 *
 * Stateless on purpose: the trait can be used by `readonly` value objects
 * (which reject traits carrying non-readonly properties), and it never
 * depends on how the composing class stores its attributes or themes.
 */
trait MergesAttributeClasses
{
    /**
     * @param array<string, int|string|null> $attributes
     *
     * @return array<string, int|string|null>
     */
    protected function mergeThemeClasses(array $attributes, ?string $themes): array
    {
        $class = isset($attributes['class'])
            ? $attributes['class'].' '
            : '';

        $merged = trim($class.($themes ?? ''));

        if ($merged !== '') {
            $attributes['class'] = $merged;
        }

        return $attributes;
    }
}
