<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Tests;

use Juaniquillo\BaseBackendComponent\BaseBackendComponent;
use Juaniquillo\BaseBackendComponent\Components\DefaultAttributeBag;
use Juaniquillo\BaseBackendComponent\Themes\GenericThemeManager;
use PHPUnit\Framework\TestCase;

final class BaseBackendComponentTest extends TestCase
{
    public function testAttributes(): void
    {
        $component = new BaseBackendComponent('div');

        $component->setAttribute('id', 'my-id');
        $component->setAttributes(['class' => 'custom-class', 'data-foo' => 'bar']);

        $this->assertSame('my-id', $component->getAttribute('id'));
        $this->assertSame(
            ['id' => 'my-id', 'class' => 'custom-class', 'data-foo' => 'bar'],
            $component->getAttributes()
        );
        $this->assertNull($component->getAttribute('missing'));
    }

    public function testContents(): void
    {
        $component = new BaseBackendComponent('div');

        $component->setContent('Hello');
        $component->setContent('World', 'key_1');
        $component->prependContent('First');
        $component->prependContent('Zero', 'key_0');

        $this->assertSame('Zero', $component->getContent('key_0'));
        $this->assertSame('Hello', $component->getContent(1));
        $this->assertSame('World', $component->getContent('key_1'));
        $this->assertSame('First', $component->getContent(0));

        $component->setContents(['replaced' => 'Replaced'], overwrite: true);

        $this->assertSame('Replaced', $component->getContent('replaced'));

        $component->unsetContent('key_1');

        $this->assertNull($component->getContent('key_1'));

        $component->unsetContent();

        $this->assertSame([], $component->getContents());
    }

    public function testSettings(): void
    {
        $component = new BaseBackendComponent('div');

        $component->setSetting('transition', 'fade');
        $component->setSettings(['size' => 'lg']);

        $this->assertSame('fade', $component->getSetting('transition'));
        $this->assertSame(['transition' => 'fade', 'size' => 'lg'], $component->getSettings());

        $component->unsetSetting('size');

        $this->assertSame(['transition' => 'fade'], $component->getSettings());
    }

    public function testPath(): void
    {
        $component = new BaseBackendComponent('div');

        $this->assertSame('div', $component->getName());
        $this->assertNull($component->getPathOnly());

        $component->setPath('form.inputs');

        $this->assertSame('form.inputs', $component->getPathOnly());
        $this->assertSame('form.inputs', $component->getPath());
    }

    public function testNameFromBackedEnum(): void
    {
        $component = new BaseBackendComponent(ComponentName::SPAN);

        $this->assertSame('span', $component->getName());
    }

    public function testAttributeBagWithoutThemesCompilesToNull(): void
    {
        $component = new BaseBackendComponent('div');

        $bag = $component->getAttributeBag();

        $this->assertInstanceOf(DefaultAttributeBag::class, $bag);
        $this->assertNull($bag->themes);
    }

    public function testAttributeBagMergesThemeClasses(): void
    {
        $component = new BaseBackendComponent(
            'button',
            new GenericThemeManager(__DIR__.'/Fixtures/themes')
        );

        $component->setAttribute('class', 'custom-class');
        $component->setTheme('action', 'success');

        $attributes = $component->getAttributeBag()->getAttributes();

        $this->assertSame('custom-class bg-green-700 hover:bg-green-800', $attributes['class']);
    }

    public function testToArrayShape(): void
    {
        $component = new BaseBackendComponent('div');
        $component->setAttribute('id', 'my-id');
        $component->setContent('Hello');
        $component->setSetting('transition', 'fade');

        $array = $component->toArray();

        $this->assertSame('div', $array['name']);
        $this->assertSame(BaseBackendComponent::class, $array['component']);
        $this->assertSame(['id' => 'my-id'], $array['attributes']);
        $this->assertSame(['Hello'], $array['contents']);
        $this->assertSame(['transition' => 'fade'], $array['settings']);
        $this->assertSame([], $array['theme']['themes']);
    }
}

enum ComponentName: string
{
    case SPAN = 'span';
}
