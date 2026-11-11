<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Tests;

use Juaniquillo\BaseBackendComponent\Exceptions\IncorrectThemePathException;
use Juaniquillo\BaseBackendComponent\Exceptions\ThemeDoesNotExistsException;

use function Juaniquillo\BaseBackendComponent\processThemes;

use Juaniquillo\BaseBackendComponent\Themes\GenericThemeManager;
use PHPUnit\Framework\TestCase;

final class ThemeManagerTest extends TestCase
{
    private string $fixturePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixturePath = __DIR__.'/Fixtures/themes';
    }

    public function testProcessThemes(): void
    {
        $manager = new GenericThemeManager($this->fixturePath);

        $this->assertSame(
            'bg-green-700 hover:bg-green-800',
            $manager->processThemes(['action' => 'success'])
        );
    }

    public function testProcessThemesHelper(): void
    {
        $manager = new GenericThemeManager($this->fixturePath);

        $this->assertSame(
            'bg-blue-700 hover:bg-blue-800 bg-green-700 hover:bg-green-800',
            processThemes(['action' => ['default', 'success']], $manager)
        );
    }

    public function testProcessThemesReturnsNullForEmptyThemes(): void
    {
        $manager = new GenericThemeManager($this->fixturePath);

        $this->assertNull($manager->processThemes([]));
    }

    public function testMissingThemeFileThrows(): void
    {
        $manager = new GenericThemeManager($this->fixturePath);

        $this->expectException(ThemeDoesNotExistsException::class);

        $manager->processThemes(['missing' => 'default']);
    }

    public function testMissingThemeVariantThrows(): void
    {
        $manager = new GenericThemeManager($this->fixturePath);

        $this->expectException(ThemeDoesNotExistsException::class);

        $manager->processThemes(['action' => 'nope']);
    }

    public function testIncorrectPathThrows(): void
    {
        $manager = new GenericThemeManager('/path/that/does/not/exist');

        $this->expectException(IncorrectThemePathException::class);

        $manager->processThemes(['action' => 'default']);
    }

    public function testThemeExtensionIsConfigurable(): void
    {
        $manager = new GenericThemeManager($this->fixturePath);

        $this->assertSame('.php', $manager->getThemeExtension());

        $manager->setThemeExtension('.blade.php');

        $this->assertSame('.blade.php', $manager->getThemeExtension());
    }
}
