<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Cache;

use Psr\SimpleCache\CacheInterface;

final readonly class FileCache implements CacheInterface
{
    public function __construct(
        private string $directory,
    ) {
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0o755, recursive: true);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->validateKey($key);

        $file = $this->getFilePath($key);

        if (!is_file($file)) {
            return $default;
        }

        $data = $this->unserializeFile($file);

        if ($data === false) {
            return $default;
        }

        if (isset($data['expiry']) && $data['expiry'] <= time()) {
            unlink($file);

            return $default;
        }

        return $data['value'];
    }

    public function set(string $key, mixed $value, int|\DateInterval|null $ttl = null): bool
    {
        $this->validateKey($key);

        $expiry = $this->resolveExpiry($ttl);

        /** @var array{value: mixed, expiry: ?int} $data */
        $data = [
            'value' => $value,
            'expiry' => $expiry,
        ];

        return $this->serializeToFile($this->getFilePath($key), $data);
    }

    public function delete(string $key): bool
    {
        $this->validateKey($key);

        $file = $this->getFilePath($key);

        if (!is_file($file)) {
            return true;
        }

        return unlink($file);
    }

    public function clear(): bool
    {
        $files = glob($this->directory.'/*.cache');

        if ($files === false) {
            // @codeCoverageIgnoreStart
            // glob() only fails on filesystem errors.
            return true;
            // @codeCoverageIgnoreEnd
        }

        $success = true;

        foreach ($files as $file) {
            // @codeCoverageIgnoreStart
            // unlink() failure requires OS-level permission errors.
            if (!unlink($file)) {
                $success = false;
            }
            // @codeCoverageIgnoreEnd
        }

        return $success;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $results = [];

        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }

        return $results;
    }

    /** @param iterable<string, mixed> $values */
    public function setMultiple(iterable $values, int|\DateInterval|null $ttl = null): bool
    {
        $success = true;

        foreach ($values as $key => $value) {
            // @codeCoverageIgnoreStart
            // set() only returns false on filesystem write failure.
            if (!$this->set($key, $value, $ttl)) {
                $success = false;
            }
            // @codeCoverageIgnoreEnd
        }

        return $success;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $success = true;

        foreach ($keys as $key) {
            // @codeCoverageIgnoreStart
            // delete() only returns false on filesystem failure.
            if (!$this->delete($key)) {
                $success = false;
            }
            // @codeCoverageIgnoreEnd
        }

        return $success;
    }

    public function has(string $key): bool
    {
        $this->validateKey($key);

        $file = $this->getFilePath($key);

        if (!is_file($file)) {
            return false;
        }

        $data = $this->unserializeFile($file);

        if ($data === false) {
            return false;
        }

        if (isset($data['expiry']) && $data['expiry'] <= time()) {
            unlink($file);

            return false;
        }

        return true;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    private function getFilePath(string $key): string
    {
        return $this->directory.'/'.$key.'.cache';
    }

    private function validateKey(string $key): void
    {
        if (!preg_match('/^[a-zA-Z0-9_.]+$/', $key)) {
            throw new InvalidCacheArgumentException(\sprintf('Cache key "%s" contains invalid characters. Only [a-zA-Z0-9_.] are allowed.', $key));
        }
    }

    private function resolveExpiry(int|\DateInterval|null $ttl): ?int
    {
        if ($ttl === null) {
            return null;
        }

        if ($ttl instanceof \DateInterval) {
            return (new \DateTimeImmutable())->add($ttl)->getTimestamp();
        }

        return time() + $ttl;
    }

    /** @param array{value: mixed, expiry: ?int} $data */
    private function serializeToFile(string $file, array $data): bool
    {
        $serialized = serialize($data);

        return file_put_contents($file, $serialized) !== false;
    }

    /** @return array{value: mixed, expiry: ?int}|false */
    private function unserializeFile(string $file): array|false
    {
        $contents = file_get_contents($file);

        if ($contents === false) {
            // @codeCoverageIgnoreStart
            // Only reachable when the file disappears between is_file() and
            // this read.
            return false;
            // @codeCoverageIgnoreEnd
        }

        /** @var array{value: mixed, expiry: ?int}|false $result */
        $result = unserialize($contents);

        return $result;
    }
}
