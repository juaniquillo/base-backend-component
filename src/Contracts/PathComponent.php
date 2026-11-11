<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Contracts;

interface PathComponent
{
    public function getName(): int|string;

    public function getPath(): string;

    public function getPathOnly(): ?string;

    public function setPath(string $path): static;
}
