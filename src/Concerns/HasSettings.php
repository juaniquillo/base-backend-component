<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Concerns;

trait HasSettings
{
    /**
     * @var array<string, bool|string>
     */
    private array $settings = [];

    public function setSetting(string $name, bool|string $value): static
    {
        $this->settings[$name] = $value;

        return $this;
    }

    /**
     * @param array<string, bool|string> $settings
     */
    public function setSettings(array $settings): static
    {
        foreach ($settings as $name => $value) {
            $this->setSetting($name, $value);
        }

        return $this;
    }

    public function getSetting(string $name): bool|string
    {
        return $this->settings[$name];
    }

    /**
     * @return array<string, bool|string>
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    public function unsetSetting(string $name): static
    {
        unset($this->settings[$name]);

        return $this;
    }
}
