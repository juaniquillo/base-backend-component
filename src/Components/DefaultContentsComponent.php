<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Components;

use Juaniquillo\BaseBackendComponent\Concerns\SerializesContents;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;
use Juaniquillo\BaseBackendComponent\Contracts\CompoundComponent;
use Juaniquillo\BaseBackendComponent\Contracts\ContentsComponent;

/**
 * Example contents collection. Treat it as a blueprint: flavor packages
 * compose `SerializesContents` into their own value object instead of
 * extending this class.
 */
final readonly class DefaultContentsComponent implements ContentsComponent
{
    use SerializesContents;

    /**
     * @param array<string|int, int|string|CompoundComponent|BackendComponent> $contents
     */
    public function __construct(
        private array $contents
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
