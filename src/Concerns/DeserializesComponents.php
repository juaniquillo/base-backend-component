<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Concerns;

use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;
use Juaniquillo\BaseBackendComponent\Contracts\CompoundComponent;
use Juaniquillo\BaseBackendComponent\Contracts\ThemeManager;

/**
 * Example deserializer. Treat it as a blueprint: flavor packages compose
 * `DeserializesComponents` into their own deserializer and override
 * `initComponent()` when their constructor signature differs.
 *
 * @phpstan-import-type ComponentArray from BackendComponent
 * @phpstan-import-type ComponentInputArray from BackendComponent
 */
trait DeserializesComponents
{
    /**
     * @param ComponentInputArray $componentArray
     */
    public static function fromArray(array $componentArray): BackendComponent|CompoundComponent
    {
        return (new self())->resolveComponent($componentArray);
    }

    /**
     * @param array{
     *  name: int|string,
     *  component: class-string<BackendComponent|CompoundComponent>,
     * } $componentArray
     */
    public function initComponent(array $componentArray): BackendComponent|CompoundComponent
    {
        $componentClass = $componentArray['component'];

        return new $componentClass($componentArray['name']);
    }

    /**
     * @param class-string<ThemeManager> $managerClass
     */
    public function resolveThemeManager(string $managerClass, string $path): ThemeManager
    {
        $manager = new $managerClass();
        $manager->setDefaultPath($path);

        return $manager;
    }

    /**
     * @param ComponentInputArray $componentArray
     */
    private function resolveComponent(array $componentArray): BackendComponent|CompoundComponent
    {
        /** @var CompoundComponent $component */
        $component = $this->initComponent($componentArray);

        $component->setAttributes($componentArray['attributes']);

        $contents = $componentArray['contents'] ?? null;
        if ($contents) {
            // overwrite: true preserves the original content keys,
            // otherwise setContents() would reindex everything.
            $component->setContents(
                $this->resolveArrayContents($contents),
                overwrite: true
            );
        }

        if ($componentArray['theme'] ?? null) {
            $themeManager = $this->resolveThemeManager(
                $componentArray['theme']['manager'],
                $componentArray['theme']['path']
            );

            $component->setThemeManager($themeManager);
            $component->setThemes($componentArray['theme']['themes']);
        }

        if (($componentArray['settings'] ?? null) && \count($componentArray['settings'])) {
            $component->setSettings($componentArray['settings']);
        }

        if ($componentArray['path'] ?? null) {
            $component->setPath($componentArray['path']);
        }

        return $component;
    }

    /**
     * @param array<string|int, string|int|array<string, mixed>> $contentsArray
     *
     * @return array<string|int, string|int|CompoundComponent|BackendComponent>
     */
    private function resolveArrayContents(array $contentsArray): array
    {
        $contents = [];

        foreach ($contentsArray as $name => $content) {
            if (!\is_array($content)) {
                $contents[$name] = $content;

                continue;
            }

            $this->assertComponentArray($content, $name);

            $contents[$name] = $this->resolveComponent($content);
        }

        return $contents;
    }

    /**
     * Nested contents are recursive, which a flat array shape cannot
     * express. Validating the shape here lets PHPStan narrow the value
     * instead of silencing the type error.
     *
     * @phpstan-assert ComponentInputArray $value
     *
     * @throws \InvalidArgumentException
     */
    private function assertComponentArray(mixed $value, string|int $key): void
    {
        if (
            !\is_array($value)
            || !isset($value['name'], $value['component'], $value['attributes'])
            || (!\is_string($value['name']) && !\is_int($value['name']))
            || !\is_string($value['component'])
            || !\is_array($value['attributes'])
        ) {
            throw new \InvalidArgumentException("Invalid component array at contents key '{$key}'.");
        }
    }
}
