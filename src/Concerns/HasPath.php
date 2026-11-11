<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Concerns;

use function Juaniquillo\BaseBackendComponent\isBackedEnum;

trait HasPath
{
    private ?string $path = null;

    public function getName(): int|string
    {
        $name = $this->name;

        if (isBackedEnum($name)) {
            return $name->value;
        }

        return $name;
    }

    public function getPath(): string
    {
        return trim((string) $this->getPathOnly(), '.');
    }

    public function getPathOnly(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }
}
