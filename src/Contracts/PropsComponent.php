<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Contracts;

/**
 * Allows components to carry rich (non-scalar) property values (booleans,
 * arrays, collections, paginators) alongside the scalar attributes defined
 * by BackendComponent.
 *
 * Scalar attributes keep flowing through setAttribute()/getAttribute(), so
 * implementations stay conformant with BackendComponent. They are
 * deliberately left out of toArray(), keeping exports JSON-safe and
 * round-trippable.
 */
interface PropsComponent
{
    public function setProp(string $name, mixed $value): static;

    public function getProp(string $name): mixed;

    /**
     * @return array<string, mixed>
     */
    public function getProps(): array;

    /**
     * @param array<string, mixed> $props
     */
    public function setProps(array $props): static;
}
