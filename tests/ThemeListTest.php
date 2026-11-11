<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Tests;

use Juaniquillo\BaseBackendComponent\Utils\ThemeList;
use PHPUnit\Framework\TestCase;

final class ThemeListTest extends TestCase
{
    private string $fixturePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixturePath = __DIR__.'/Fixtures/themes';
    }

    public function testMakeReturnsInstance(): void
    {
        $this->assertInstanceOf(ThemeList::class, ThemeList::make($this->fixturePath));
    }

    public function testScanFilesReturnsThemesWithMetadata(): void
    {
        $themes = ThemeList::make($this->fixturePath)->scanFiles();

        $this->assertArrayHasKey('action', $themes);
        $this->assertSame('action.php', $themes['action']['file']);
        $this->assertSame(
            [
                'default' => 'bg-blue-700 hover:bg-blue-800',
                'success' => 'bg-green-700 hover:bg-green-800',
                'error' => 'bg-red-700 hover:bg-red-800',
            ],
            $themes['action']['themes']
        );
        $this->assertSame(realpath($this->fixturePath.'/action.php'), $themes['action']['path']);
    }

    public function testScanFilesSkipsSubdirectories(): void
    {
        $themes = ThemeList::make($this->fixturePath)->scanFiles();

        $this->assertArrayNotHasKey('nested', $themes);
        $this->assertArrayNotHasKey('ignored', $themes);
    }

    public function testScanFilesWithIncorrectPathThrows(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('incorrect');

        ThemeList::make($this->fixturePath.'/does-not-exist')->scanFiles();
    }

    public function testGetThemesWithMissingFileThrows(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('missing.php');

        ThemeList::make($this->fixturePath)->getThemes(['missing.php']);
    }

    public function testGetThemesIgnoresDotEntries(): void
    {
        $themes = ThemeList::make($this->fixturePath)->getThemes(['.', '..', 'action.php']);

        $this->assertSame(['action'], array_keys($themes));
    }
}
