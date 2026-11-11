<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Components;

use Juaniquillo\BaseBackendComponent\Concerns\MergesAttributeClasses;
use Juaniquillo\BaseBackendComponent\Contracts\AttributeBag;
use Juaniquillo\BaseBackendComponent\Contracts\ContentsComponent;

/**
 * Example attribute bag. Treat it as a blueprint: flavor packages compose
 * the traits they need into their own bag instead of extending this class,
 * so they are free to add or drop concerns (Livewire state, extra payloads,
 * different theme inputs, ...).
 */
final readonly class DefaultAttributeBag implements AttributeBag
{
    use MergesAttributeClasses;

    public function __construct(
        /** @var array<string, int|string|null> $attributes */
        private array $attributes,
        public ?ContentsComponent $content = null,
        public ?string $themes = null,
        public ?string $path = null,
        /** @var array<string, bool|string> $settings */
        public array $settings = [],
        /** @var array<string, mixed> $props */
        public array $props = [],
    ) {
    }

    /** @return  array<string, int|string|null> */
    public function getAttributes(): array
    {
        return $this->mergeThemeClasses($this->attributes, $this->themes);
    }

    /** @return array<string, mixed> */
    public function getProps(): array
    {
        return $this->props;
    }

    /** @return array<string, mixed> */
    public function getAttributesAndProps(): array
    {
        return array_merge($this->getAttributes(), $this->props);
    }
}
