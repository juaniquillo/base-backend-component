<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Tests;

use Juaniquillo\BaseBackendComponent\BaseBackendComponent;
use Juaniquillo\BaseBackendComponent\Concerns\MergesAttributeClasses;
use Juaniquillo\BaseBackendComponent\Concerns\SerializesContents;
use Juaniquillo\BaseBackendComponent\Contracts\AttributeBag;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;
use Juaniquillo\BaseBackendComponent\Contracts\CompoundComponent;
use Juaniquillo\BaseBackendComponent\Contracts\ContentsComponent;
use PHPUnit\Framework\TestCase;

/**
 * Flavor packages build their own classes by composing traits, never by
 * extending the example classes. These tests exercise exactly that.
 */
final class CompositionTest extends TestCase
{
    public function testFlavorBagComposesTheMergingTrait(): void
    {
        $bag = new FlavorAttributeBag(
            attributes: ['class' => 'text-sm'],
            themes: 'bg-blue-700',
        );

        $this->assertSame(['class' => 'text-sm bg-blue-700'], $bag->getAttributes());
    }

    public function testFlavorBagKeepsItsOwnExtras(): void
    {
        $bag = new FlavorAttributeBag(
            attributes: ['id' => 'save'],
            extras: ['wire:click' => 'save'],
        );

        $this->assertSame(
            ['id' => 'save', 'wire:click' => 'save'],
            $bag->getAttributesAndProps()
        );
    }

    public function testMergingTraitSkipsEmptyThemes(): void
    {
        $bag = new FlavorAttributeBag(attributes: ['id' => 'save']);

        $this->assertSame(['id' => 'save'], $bag->getAttributes());
    }

    public function testMergingTraitAppendsToExistingClass(): void
    {
        $bag = new FlavorAttributeBag(attributes: ['class' => 'a'], themes: 'b c');

        $this->assertSame(['class' => 'a b c'], $bag->getAttributes());
    }

    public function testFlavorContentsComposesTheSerializingTrait(): void
    {
        $child = new BaseBackendComponent('span');
        $child->setContent('Hello');

        $contents = new FlavorContentsComponent([
            'child' => $child,
            'text' => 'World',
        ]);

        $this->assertSame('World', $contents->getContents()['text']);
        $this->assertSame(
            [
                'child' => $child->toArray(),
                'text' => 'World',
            ],
            $contents->toArray()
        );
    }
}

final readonly class FlavorAttributeBag implements AttributeBag
{
    use MergesAttributeClasses;

    /**
     * @param array<string, int|string|null> $attributes
     * @param array<string, mixed>           $extras
     */
    public function __construct(
        private array $attributes,
        private ?string $themes = null,
        private array $extras = [],
    ) {
    }

    /**
     * @return array<string, int|string|null>
     */
    public function getAttributes(): array
    {
        return $this->mergeThemeClasses($this->attributes, $this->themes);
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributesAndProps(): array
    {
        return array_merge($this->getAttributes(), $this->extras);
    }
}

final readonly class FlavorContentsComponent implements ContentsComponent
{
    use SerializesContents;

    /**
     * @param array<string|int, int|string|CompoundComponent|BackendComponent> $contents
     */
    public function __construct(
        private array $contents,
    ) {
    }

    /**
     * @return array<string|int, string|int|CompoundComponent|BackendComponent>
     */
    public function getContents(): array
    {
        return $this->contents;
    }
}
