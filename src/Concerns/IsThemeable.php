<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Concerns;

use Juaniquillo\BaseBackendComponent\Contracts\ThemeManager;

trait IsThemeable
{
    /**
     * @var array<string, string|array<string|int, string>>
     */
    private array $themes = [];

    private ThemeManager $themeManager;

    public function setThemeManager(ThemeManager $themeManager): static
    {
        $this->themeManager = $themeManager;

        return $this;
    }

    /**
     * @return array<string, string|array<string|int, string>>
     */
    public function getThemes(): array
    {
        return $this->themes;
    }

    /**
     * @return string|array<string|int, string>|null
     */
    public function getTheme(string $name): string|array|null
    {
        return $this->getThemes()[$name] ?? null;
    }

    public function getThemeManager(): ThemeManager
    {
        return $this->themeManager;
    }

    /**
     * @param string|array<int|string, string> $theme
     */
    public function setTheme(string $name, string|array $theme, bool $overwrite = false): static
    {
        if ($overwrite) {
            $this->themes[$name] = $theme;

            return $this;
        }

        $existing = $this->themes[$name] ?? null;

        if ($existing === null) {
            $this->themes[$name] = $theme;

            return $this;
        }

        $existingArray = \is_array($existing) ? $existing : [$existing];
        $newValues = \is_array($theme) ? $theme : [$theme];

        foreach ($newValues as $value) {
            if (!\in_array($value, $existingArray, true)) {
                $existingArray[] = $value;
            }
        }

        $this->themes[$name] = $existingArray;

        return $this;
    }

    /**
     * @param array<string, string|array<int|string, string>> $themes
     */
    public function setThemes(array $themes, bool $overwrite = false): static
    {
        foreach ($themes as $name => $theme) {
            $this->setTheme($name, $theme, $overwrite);
        }

        return $this;
    }

    public function compileTheme(): ?string
    {
        $themes = $this->getThemes();

        if (!\count($themes)) {
            return null;
        }

        return $this->getThemeManager()
            ->processThemes(themes: $themes);
    }
}
