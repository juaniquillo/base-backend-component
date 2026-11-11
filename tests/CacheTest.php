<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Tests;

use Juaniquillo\BaseBackendComponent\Cache\DefaultCache;
use Juaniquillo\BaseBackendComponent\Cache\FileCache;
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
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

    public function testFileCache(): void
    {
        $directory = sys_get_temp_dir().'/base-backend-component-tests-'.uniqid();

        $cache = new FileCache($directory);

        try {
            $cache->set('key', 'value');

            $this->assertTrue($cache->has('key'));
            $this->assertSame('value', $cache->get('key'));
            $this->assertSame('fallback', $cache->get('missing', 'fallback'));

            $cache->delete('key');

            $this->assertFalse($cache->has('key'));
        } finally {
            $cache->clear();

            /** @var array<int, string> $files */
            $files = glob($directory.'/*') ?: [];

            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            if (is_dir($directory)) {
                rmdir($directory);
            }
        }
    }
}
