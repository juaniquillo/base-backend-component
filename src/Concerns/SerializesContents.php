<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Concerns;

use function Juaniquillo\BaseBackendComponent\isComponent;

/**
 * Serializes a contents collection into nested arrays.
 *
 * The composing class only has to provide `getContents()`; the recursive
 * walk into child components lives here so flavor implementations do not
 * copy it.
 */
trait SerializesContents
{
    /**
     * @return array<string|int, string|int|array<string, mixed>>
     */
    public function toArray(): array
    {
        $contentArray = [];

        foreach ($this->getContents() as $key => $content) {
            $contentArray[$key] = isComponent($content) ? $content->toArray() : $content;
        }

        return $contentArray;
    }
}
