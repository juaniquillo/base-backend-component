<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Tests;

use Juaniquillo\BaseBackendComponent\Cache\DefaultCache;
use Juaniquillo\BaseBackendComponent\Cache\FileCache;
use Juaniquillo\BaseBackendComponent\Cache\InvalidCacheArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir().'/base-backend-component-tests-'.bin2hex(random_bytes(6));
    }

    protected function tearDown(): void
    {
        $files = glob($this->directory.'/*') ?: [];

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        if (is_dir($this->directory)) {
            rmdir($this->directory);
        }

        parent::tearDown();
    }

    public function testDefaultCache(): void
    {
        $cache = new DefaultCache();

        $this->assertFalse($cache->has('key'));

        $cache->set('key', 'value');

        $this->assertTrue($cache->has('key'));
        $this->assertSame('value', $cache->get('key'));

        $cache->delete('key');

        $this->assertFalse($cache->has('key'));
        $this->assertNull($cache->get('key'));
    }

    public function testFileCacheStoresReadsAndDeletesValues(): void
    {
        $cache = new FileCache($this->directory);

        $this->assertSame($this->directory, $cache->getDirectory());
        $this->assertFalse($cache->has('key'));
        $this->assertSame('fallback', $cache->get('key', 'fallback'));

        $this->assertTrue($cache->set('key', 'value'));
        $this->assertTrue($cache->has('key'));
        $this->assertSame('value', $cache->get('key'));

        $this->assertTrue($cache->delete('key'));
        $this->assertFalse($cache->has('key'));

        // Deleting a missing key is not an error.
        $this->assertTrue($cache->delete('key'));
    }

    public function testFileCacheCreatesMissingDirectory(): void
    {
        new FileCache($this->directory);

        $this->assertDirectoryExists($this->directory);

        // Constructing again over an existing directory is a no-op.
        $cache = new FileCache($this->directory);

        $this->assertSame($this->directory, $cache->getDirectory());
    }

    public function testFileCacheClearsEveryEntry(): void
    {
        $cache = new FileCache($this->directory);

        $cache->set('one', 1);
        $cache->set('two', 2);

        $this->assertTrue($cache->clear());
        $this->assertFalse($cache->has('one'));
        $this->assertFalse($cache->has('two'));
    }

    public function testFileCacheClearOnEmptyDirectoryReturnsTrue(): void
    {
        $cache = new FileCache($this->directory);

        $this->assertTrue($cache->clear());
    }

    public function testFileCacheExpiresIntegerTtlOnGet(): void
    {
        $cache = new FileCache($this->directory);
        $cache->set('expired', 'value', -1);

        $this->assertSame('fallback', $cache->get('expired', 'fallback'));
        $this->assertFalse($cache->has('expired'));
    }

    public function testFileCacheExpiresIntegerTtlOnHas(): void
    {
        $cache = new FileCache($this->directory);
        $cache->set('expired', 'value', -1);

        $this->assertFalse($cache->has('expired'));
        $this->assertFalse(is_file($this->directory.'/expired.cache'));
    }

    public function testFileCacheAcceptsDateIntervalTtl(): void
    {
        $cache = new FileCache($this->directory);
        $cache->set('key', 'value', new \DateInterval('PT1H'));

        $this->assertTrue($cache->has('key'));
        $this->assertSame('value', $cache->get('key'));
    }

    public function testFileCacheReturnsDefaultWhenStoredDataIsUnusable(): void
    {
        $cache = new FileCache($this->directory);

        file_put_contents($this->directory.'/broken.cache', serialize(false));

        $this->assertSame('fallback', $cache->get('broken', 'fallback'));
        $this->assertFalse($cache->has('broken'));
    }

    public function testFileCacheHandlesMultipleKeys(): void
    {
        $cache = new FileCache($this->directory);

        $this->assertTrue($cache->setMultiple(['a' => 1, 'b' => 2]));
        $this->assertSame(['a' => 1, 'b' => 2], $cache->getMultiple(['a', 'b']));
        $this->assertSame(['a' => 1, 'c' => 'fallback'], $cache->getMultiple(['a', 'c'], 'fallback'));

        $this->assertTrue($cache->deleteMultiple(['a', 'b']));

        $this->assertFalse($cache->has('a'));
        $this->assertFalse($cache->has('b'));
    }

    #[DataProvider('provideCacheMethods')]
    public function testFileCacheRejectsInvalidKeys(string $method): void
    {
        $cache = new FileCache($this->directory);

        $this->expectException(InvalidCacheArgumentException::class);

        if ($method === 'get') {
            $cache->get('invalid key');
        }

        if ($method === 'set') {
            $cache->set('invalid key', 'value');
        }

        if ($method === 'has') {
            $cache->has('invalid key');
        }

        if ($method === 'delete') {
            $cache->delete('invalid key');
        }
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function provideCacheMethods(): iterable
    {
        yield 'get' => ['get'];
        yield 'set' => ['set'];
        yield 'has' => ['has'];
        yield 'delete' => ['delete'];
    }
}
