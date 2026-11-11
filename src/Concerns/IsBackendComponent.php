<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Concerns;

trait IsBackendComponent
{
    /**
     * @var array<string, int|string|null>
     */
    private array $attributes = [];

    /**
     * @return array<string, int|string|null>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $name): int|string|null
    {
        return $this->getAttributes()[$name] ?? null;
    }

    public function setAttribute(string $name, int|string|null $content): static
    {
        $this->attributes[$name] = $content;

        return $this;
    }

    /**
     * @param array<string, int|string|null> $attributes
     */
    public function setAttributes(array $attributes): static
    {
        foreach ($attributes as $name => $content) {
            $this->setAttribute($name, $content);
        }

        return $this;
    }
}
