<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Contracts;

interface ContentsComponent
{
    /**
     * @return array<string|int, string|int|CompoundComponent|BackendComponent>
     */
    public function getContents(): array;

    /**
     * @return array<string|int, string|int|array<string, mixed>>
     */
    public function toArray(): array;
}
