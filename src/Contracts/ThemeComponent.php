<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Contracts;

interface ThemeComponent
{
    public function setThemeManager(ThemeManager $themeManager): static;

    /**
     * @param string|array<int|string, string> $theme
     */
    public function setTheme(string $name, string|array $theme, bool $overwrite = false): static;

    /**
     * @param array<string, string|array<string|int, string>> $themes
     */
    public function setThemes(array $themes, bool $overwrite = false): static;

    /**
     * @return array<string, string|array<string|int, string>>
     */
    public function getThemes(): array;

    /**
     * @return string|array<string|int, string>|null
     */
    public function getTheme(string $name): string|array|null;

    public function getThemeManager(): ThemeManager;

    public function compileTheme(): ?string;
}
