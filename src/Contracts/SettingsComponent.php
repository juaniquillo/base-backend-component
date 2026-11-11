<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Contracts;

interface SettingsComponent
{
    public function setSetting(string $name, bool|string $value): static;

    /**  @param  array<string, bool|string>  $settings */
    public function setSettings(array $settings): static;

    public function getSetting(string $name): bool|string;

    /** @return array<string, bool|string> */
    public function getSettings(): array;

    public function unsetSetting(string $name): static;
}
