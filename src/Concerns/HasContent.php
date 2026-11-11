<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Concerns;

use Juaniquillo\BaseBackendComponent\Components\DefaultContentsComponent;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;
use Juaniquillo\BaseBackendComponent\Contracts\CompoundComponent;
use Juaniquillo\BaseBackendComponent\Contracts\ContentsComponent;

trait HasContent
{
    /**
     * @var array<string|int, string|int|CompoundComponent|BackendComponent>
     */
    private array $content = [];

    public function getContent(string|int $key): CompoundComponent|BackendComponent|int|string|null
    {
        return $this->content[$key] ?? null;
    }

    /**
     * @return array<string|int, string|int|CompoundComponent|BackendComponent>
     */
    public function getContents(): array
    {
        return $this->content;
    }

    public function setContent(int|string|CompoundComponent|BackendComponent $content, string|int|null $key = null): static
    {
        if ($key) {
            $this->content[$key] = $content;

            return $this;
        }

        $this->content[] = $content;

        return $this;
    }

    /**
     * @param array<string|int, string|int|CompoundComponent|BackendComponent> $contents
     */
    public function setContents(array $contents, bool $overwrite = false): static
    {
        foreach ($contents as $key => $content) {
            $this->setContent($content, $overwrite ? $key : null);
        }

        return $this;
    }

    public function prependContent(int|string|CompoundComponent|BackendComponent $content, string|int|null $key = null): static
    {
        if ($key) {
            $this->content = [$key => $content] + $this->content;

            return $this;
        }

        array_unshift($this->content, $content);

        return $this;
    }

    public function unsetContent(string|int|null $key = null): static
    {
        if ($key) {
            unset($this->content[$key]);

            return $this;
        }

        $this->content = [];

        return $this;
    }

    public function processContent(): ContentsComponent
    {
        return new DefaultContentsComponent($this->getContents());
    }
}
