<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Tests;

use Juaniquillo\BaseBackendComponent\Components\DefaultAttributeBag;
use PHPUnit\Framework\TestCase;

final class DefaultAttributeBagTest extends TestCase
{
    public function testGetAttributesMergesThemesIntoClass(): void
    {
        $bag = new DefaultAttributeBag(
            attributes: ['id' => 'save', 'class' => 'text-sm'],
            themes: 'bg-blue-700',
        );

        $this->assertSame(
            ['id' => 'save', 'class' => 'text-sm bg-blue-700'],
            $bag->getAttributes()
        );
    }

    public function testGetAttributesLeavesClassAloneWithoutThemes(): void
    {
        $bag = new DefaultAttributeBag(attributes: ['class' => 'text-sm']);

        $this->assertSame(['class' => 'text-sm'], $bag->getAttributes());
    }

    public function testGetAttributesAddsClassOnlyThemes(): void
    {
        $bag = new DefaultAttributeBag(attributes: ['id' => 'save'], themes: 'bg-blue-700');

        $this->assertSame(['id' => 'save', 'class' => 'bg-blue-700'], $bag->getAttributes());
    }

    public function testGetProps(): void
    {
        $bag = new DefaultAttributeBag(attributes: [], props: ['page' => 2]);

        $this->assertSame(['page' => 2], $bag->getProps());
    }

    public function testGetAttributesAndProps(): void
    {
        $bag = new DefaultAttributeBag(
            attributes: ['id' => 'save'],
            themes: 'bg-blue-700',
            props: ['page' => 2],
        );

        $this->assertSame(
            ['id' => 'save', 'class' => 'bg-blue-700', 'page' => 2],
            $bag->getAttributesAndProps()
        );
    }
}
