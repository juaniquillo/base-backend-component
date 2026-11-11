<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Contracts;

/**
 * @template T
 */
interface Cache
{
    /** @return T */
    public function get(string $key): mixed;

    /** @param  T  $value */
    public function set(string $key, mixed $value): void;

    public function has(string $key): bool;

    public function delete(string $key): void;
}
