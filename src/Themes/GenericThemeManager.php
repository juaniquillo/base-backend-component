<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Themes;

use Juaniquillo\BaseBackendComponent\Concerns\IsThemeManager;
use Juaniquillo\BaseBackendComponent\Contracts\ThemeManager;

/**
 * Minimal theme manager with no bundled themes: pass the directory that
 * holds your theme files. Treat it as a blueprint, compose the
 * `IsThemeManager` trait when you need different defaults.
 */
final class GenericThemeManager implements ThemeManager
{
    use IsThemeManager;

    /**
     * Defaults to the system temp directory so that toArray() never fails
     * on components that never compile themes. Pass an explicit path as
     * soon as the component actually resolves theme files.
     */
    public function __construct(?string $defaultPath = null)
    {
        $this->setDefaultPath($defaultPath ?? sys_get_temp_dir());
    }
}
