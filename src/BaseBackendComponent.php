<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent;

use Juaniquillo\BaseBackendComponent\Components\DefaultAttributeBag;
use Juaniquillo\BaseBackendComponent\Concerns\HasContent;
use Juaniquillo\BaseBackendComponent\Concerns\HasPath;
use Juaniquillo\BaseBackendComponent\Concerns\HasSettings;
use Juaniquillo\BaseBackendComponent\Concerns\IsBackendComponent;
use Juaniquillo\BaseBackendComponent\Concerns\IsThemeable;
use Juaniquillo\BaseBackendComponent\Contracts\AttributeBag;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;
use Juaniquillo\BaseBackendComponent\Contracts\CompoundComponent;
use Juaniquillo\BaseBackendComponent\Contracts\ThemeManager;
use Juaniquillo\BaseBackendComponent\Themes\GenericThemeManager;

/**
 * Example compound component. Treat it as a blueprint: flavor packages
 * compose the traits they need into their own class instead of extending
 * this one, so they only carry the concerns their rendering actually uses.
 *
 * @phpstan-import-type ComponentArray from BackendComponent
 */
final class BaseBackendComponent implements CompoundComponent
{
    use HasContent;
    use HasPath;
    use HasSettings;
    use IsBackendComponent;
    use IsThemeable;

    public function __construct(
        private string|\BackedEnum $name,
        ThemeManager $themeManager = new GenericThemeManager()
    ) {
        $this->themeManager = $themeManager;
    }

    public function getAttributeBag(): AttributeBag
    {
        return new DefaultAttributeBag(
            attributes: $this->getAttributes(),
            content: $this->processContent(),
            themes: $this->compileTheme(),
            path: $this->getPath(),
            settings: $this->getSettings(),
        );
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
