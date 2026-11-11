<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Cache;

use Psr\SimpleCache\InvalidArgumentException;

final class InvalidCacheArgumentException extends \RuntimeException implements InvalidArgumentException
{
}
