<?php

declare(strict_types=1);

namespace Juaniquillo\BaseBackendComponent\Deserializers;

use Juaniquillo\BaseBackendComponent\Concerns\DeserializesComponents;

/**
 * Example deserializer for compound components. Treat it as a blueprint:
 * compose `DeserializesComponents` in your own class instead of extending.
 */
final class ComponentDeserializer
{
    use DeserializesComponents;
}
