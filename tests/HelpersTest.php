<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Tests;

use Juaniquillo\BaseBackendComponent\BaseBackendComponent;

use function Juaniquillo\BaseBackendComponent\cache;

use Juaniquillo\BaseBackendComponent\Cache\DefaultCache;

use function Juaniquillo\BaseBackendComponent\isBackedEnum;
use function Juaniquillo\BaseBackendComponent\isComponent;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    #[DataProvider('provideComponents')]
    public function testIsComponent(mixed $value, bool $expected): void
    {
        $this->assertSame($expected, isComponent($value));
    }

    /**
     * @return iterable<string, array{0: mixed, 1: bool}>
     */
    public static function provideComponents(): iterable
    {
        yield 'component' => [new BaseBackendComponent('div'), true];
        yield 'string' => ['div', false];
        yield 'null' => [null, false];
    }

    #[DataProvider('provideBackedEnums')]
    public function testIsBackedEnum(mixed $value, bool $expected): void
    {
        $this->assertSame($expected, isBackedEnum($value));
    }

    /**
     * @return iterable<string, array{0: mixed, 1: bool}>
     */
    public static function provideBackedEnums(): iterable
    {
        yield 'enum' => [HelpersStubEnum::A, true];
        yield 'string' => ['A', false];
    }

    public function testCacheHelperReturnsSharedInstance(): void
    {
        $first = cache('helpers-test');
        $second = cache('helpers-test');

        $this->assertInstanceOf(DefaultCache::class, $first);
        $this->assertSame($first, $second);
        $this->assertNotSame($first, cache('helpers-test-other'));
    }
}

enum HelpersStubEnum: string
{
    case A = 'a';
}
