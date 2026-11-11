<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Concerns;

use Juaniquillo\BaseBackendComponent\Contracts\PropsComponent;

/**
 * @see PropsComponent
 */
trait HasProps
{
    /**
     * @var array<string, mixed>
     */
    private array $props = [];

    public function setProp(string $name, mixed $value): static
    {
        $this->props[$name] = $value;

        return $this;
    }

    public function getProp(string $name): mixed
    {
        return $this->props[$name] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function getProps(): array
    {
        return $this->props;
    }

    /**
     * @param array<string, mixed> $props
     */
    public function setProps(array $props): static
    {
        foreach ($props as $name => $value) {
            $this->setProp($name, $value);
        }

        return $this;
    }
}
