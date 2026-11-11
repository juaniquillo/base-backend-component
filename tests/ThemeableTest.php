<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Tests;

use Juaniquillo\BaseBackendComponent\BaseBackendComponent;
use Juaniquillo\BaseBackendComponent\Themes\GenericThemeManager;
use PHPUnit\Framework\TestCase;

final class ThemeableTest extends TestCase
{
    private BaseBackendComponent $component;

    protected function setUp(): void
    {
        parent::setUp();

        $this->component = new BaseBackendComponent(
            'button',
            new GenericThemeManager(__DIR__.'/Fixtures/themes')
        );
    }

    public function testThemesAreEmptyByDefault(): void
    {
        $this->assertSame([], $this->component->getThemes());
        $this->assertNull($this->component->getTheme('action'));
        $this->assertNull($this->component->compileTheme());
    }

    public function testSetThemeReplacesWithOverwrite(): void
    {
        $this->component->setTheme('action', 'default');
        $this->component->setTheme('action', 'success', overwrite: true);

        $this->assertSame('success', $this->component->getTheme('action'));
    }

    public function testSetThemeAccumulatesWithoutDuplicates(): void
    {
        $this->component->setTheme('action', 'success');
        $this->component->setTheme('action', 'success');
        $this->component->setTheme('action', ['default', 'success']);

        $this->assertSame(['success', 'default'], $this->component->getTheme('action'));
    }

    public function testSetThemesAppliesBatch(): void
    {
        $this->component->setThemes(['action' => 'success', 'size' => 'lg']);

        $this->assertSame('success', $this->component->getTheme('action'));
        $this->assertSame('lg', $this->component->getTheme('size'));
    }

    public function testSetThemesWithOverwrite(): void
    {
        $this->component->setTheme('action', 'default');
        $this->component->setThemes(['action' => 'success'], overwrite: true);

        $this->assertSame('success', $this->component->getTheme('action'));
    }

    public function testThemeManagerCanBeReplaced(): void
    {
        $manager = new GenericThemeManager(__DIR__.'/Fixtures/themes');

        $returned = $this->component->setThemeManager($manager);

        $this->assertSame($this->component, $returned);
        $this->assertSame($manager, $this->component->getThemeManager());
    }

    public function testCompileThemeReturnsCompiledClasses(): void
    {
        $this->component->setTheme('action', 'success');

        $this->assertSame('bg-green-700 hover:bg-green-800', $this->component->compileTheme());
    }
}
