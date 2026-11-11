<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Contracts;

interface ContentComponent
{
    public function getContent(string|int $key): CompoundComponent|BackendComponent|int|string|null;

    /**
     * @return array<string|int, string|int|CompoundComponent|BackendComponent>
     */
    public function getContents(): array;

    public function setContent(int|string|CompoundComponent|BackendComponent $content, string|int|null $key = null): static;

    /**
     * @param array<string|int, string|int|CompoundComponent|BackendComponent> $contents
     */
    public function setContents(array $contents, bool $overwrite = false): static;

    public function prependContent(int|string|CompoundComponent|BackendComponent $content, string|int|null $key = null): static;

    public function unsetContent(string|int|null $key = null): static;

    public function processContent(): ContentsComponent;
}
