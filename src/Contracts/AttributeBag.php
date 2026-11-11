<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Contracts;

interface AttributeBag
{
    /** @return array<string, string> */
    public function getAttributes(): array;

    /** @return array<string, mixed> */
    public function getAttributesAndProps(): array;
}
