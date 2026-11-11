<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Cache;

use Juaniquillo\BaseBackendComponent\Contracts\Cache;

/**
 * @template T
 *
 * @implements Cache<DefaultCache<T>>
 */
class DefaultCache implements Cache
{
    /** @var array<string, T> */
    private array $values = [];

    /** @return T|null */
    public function get(string $key): mixed
    {
        return $this->values[$key] ?? null;
    }

    /** @param  T  $value */
    public function set(string $key, mixed $value): void
    {
        $this->values[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($this->values[$key]);
    }

    public function delete(string $key): void
    {
        if ($this->has($key)) {
            unset($this->values[$key]);
        }
    }
}
