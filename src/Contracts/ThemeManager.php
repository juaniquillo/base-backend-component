<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Contracts;

use Juaniquillo\BaseBackendComponent\Exceptions\IncorrectThemePathException;
use Juaniquillo\BaseBackendComponent\Exceptions\ThemeDoesNotExistsException;

interface ThemeManager
{
    public function setDefaultPath(string $path): static;

    /**
     * @throws \Exception
     */
    public function getDefaultPath(): string;

    /** @throws IncorrectThemePathException */
    public function getThemePath(): string;

    public function setThemeExtension(string $extension): static;

    public function getThemeExtension(): string;

    public function disableCache(bool $disable = true): static;

    public function getCacheHits(): int;

    public function unsetCacheHits(): static;

    /**
     * @param array<string, string|array<string|int, string>> $themes
     */
    public function processThemes(array $themes): ?string;

    /**
     * @param string|array<string, string|array<int, string>> $theme
     *
     * @throws ThemeDoesNotExistsException
     */
    public function processTheme(string $type, string|array $theme): ?string;

    /**
     * @param array<string|int, string>                       $styleGroup
     * @param array<string, array<string|int, string>|string> $style
     */
    public function resolveTheme(array $styleGroup, array $style): string;

    /**
     * @param array<string|int, string> $styleGroup
     * @param array<int, string>        $styles
     */
    public function resolveArrayThemes(array $styleGroup, array $styles): string;
}
