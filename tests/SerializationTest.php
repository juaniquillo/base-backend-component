<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Tests;

use Juaniquillo\BaseBackendComponent\BaseBackendComponent;
use Juaniquillo\BaseBackendComponent\Concerns\HasContent;
use Juaniquillo\BaseBackendComponent\Concerns\HasPath;
use Juaniquillo\BaseBackendComponent\Concerns\HasProps;
use Juaniquillo\BaseBackendComponent\Concerns\HasSettings;
use Juaniquillo\BaseBackendComponent\Concerns\IsBackendComponent;
use Juaniquillo\BaseBackendComponent\Concerns\IsThemeable;
use Juaniquillo\BaseBackendComponent\Contracts\AttributeBag;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;
use Juaniquillo\BaseBackendComponent\Contracts\IndividualComponent;
use Juaniquillo\BaseBackendComponent\Deserializers\ComponentDeserializer;
use Juaniquillo\BaseBackendComponent\Deserializers\IndividualComponentDeserializer;
use Juaniquillo\BaseBackendComponent\Themes\GenericThemeManager;
use PHPUnit\Framework\TestCase;

final class SerializationTest extends TestCase
{
    public function testRoundTrip(): void
    {
        $child = new BaseBackendComponent('span');
        $child->setContent('Hello');

        $component = new BaseBackendComponent(
            'div',
            new GenericThemeManager(__DIR__.'/Fixtures/themes')
        );
        $component->setAttribute('id', 'my-id');
        $component->setContent($child, 'child');
        $component->setTheme('action', 'success');
        $component->setSetting('transition', 'fade');
        $component->setPath('layout');

        $restored = ComponentDeserializer::fromArray($component->toArray());

        $this->assertEquals($component->toArray(), $restored->toArray());
    }

    public function testRoundTripWithoutTheme(): void
    {
        $component = new BaseBackendComponent('div');
        $component->setContents(['A', 'B']);

        $restored = ComponentDeserializer::fromArray($component->toArray());

        $this->assertEquals($component->toArray(), $restored->toArray());
    }

    public function testInvalidNestedComponentArrayThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid component array at contents key 'child'.");

        ComponentDeserializer::fromArray([
            'name' => 'div',
            'component' => BaseBackendComponent::class,
            'attributes' => [],
            'contents' => [
                'child' => ['not' => 'a component'],
            ],
        ]);
    }

    public function testIndividualComponentDeserializer(): void
    {
        $deserializer = new IndividualComponentDeserializer();

        $restored = $deserializer->fromArray([
            'name' => 'stub',
            'component' => StubIndividualComponent::class,
            'attributes' => ['id' => 'stub-id'],
        ]);

        $this->assertInstanceOf(StubIndividualComponent::class, $restored);
        $this->assertSame('stub-id', $restored->getAttribute('id'));

        $restored->setProp('page', 2);

        $this->assertSame(2, $restored->getProp('page'));
        $this->assertSame(['page' => 2], $restored->getProps());

        $restored->setProps(['page' => 3, 'sort' => 'name']);

        $this->assertSame(['page' => 3, 'sort' => 'name'], $restored->getProps());
    }
}

/**
 * @phpstan-import-type ComponentArray from BackendComponent
 */
final class StubIndividualComponent implements BackendComponent, IndividualComponent
{
    use HasContent;
    use HasPath;
    use HasProps;
    use HasSettings;
    use IsBackendComponent;
    use IsThemeable;

    private string $name = 'stub';

    public function __construct()
    {
        $this->themeManager = new GenericThemeManager();
    }

    public function getAttributeBag(): AttributeBag
    {
        throw new \LogicException('Not needed for this test.');
    }

    /**
     * @return ComponentArray
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'component' => self::class,
            'attributes' => $this->getAttributes(),
            'contents' => $this->processContent()->toArray(),
            'theme' => [
                'manager' => $this->getThemeManager()::class,
                'themes' => $this->getThemes(),
                'path' => $this->getThemeManager()->getDefaultPath(),
                'realPath' => $this->getThemeManager()->getThemePath(),
            ],
            'path' => $this->getPathOnly(),
            'settings' => $this->getSettings(),
        ];
    }
}
