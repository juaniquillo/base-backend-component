<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Contracts;

/**
 * @phpstan-type ComponentArray array{
 *  name: int|string,
 *  component: class-string<BackendComponent|CompoundComponent>,
 *  attributes: array<string, int|string|null>,
 *  contents: array<string|int, string|int|array<string, mixed>>,
 *  theme: array{
 *   manager: class-string<ThemeManager>,
 *   themes: array<string, array<int|string, string>|string>,
 *   path: string,
 *   realPath: string,
 *  },
 *  path: string|null,
 *  settings: array<string, bool|string>,
 * }
 * @phpstan-type ComponentInputArray array{
 *  name: int|string,
 *  component: class-string<BackendComponent|CompoundComponent>,
 *  attributes: array<string, int|string|null>,
 *  contents?: array<string|int, string|int|array<string, mixed>>,
 *  theme?: array{
 *   manager: class-string<ThemeManager>,
 *   themes: array<string, array<int|string, string>|string>,
 *   path: string,
 *   realPath: string,
 *  },
 *  path?: string|null,
 *  settings?: array<string, bool|string>,
 * }
 */
interface BackendComponent
{
    /**
     * @return array<string, int|string|null>
     */
    public function getAttributes(): array;

    public function getAttribute(string $name): int|string|null;

    public function getAttributeBag(): AttributeBag;

    public function setAttribute(string $name, int|string|null $content): static;

    /**
     * @param array<string, int|string|null> $attributes
     */
    public function setAttributes(array $attributes): static;

    /**
     * @return ComponentArray
     */
    public function toArray(): array;
}
