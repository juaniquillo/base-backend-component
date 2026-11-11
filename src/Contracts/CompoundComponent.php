<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Contracts;

interface CompoundComponent extends BackendComponent, ContentComponent, PathComponent, SettingsComponent, ThemeComponent
{
}
