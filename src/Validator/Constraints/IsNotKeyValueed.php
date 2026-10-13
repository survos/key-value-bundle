<?php

declare(strict_types=1);

namespace Survos\KeyValueBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
/** @Annotation */
class IsNotKeyValueed extends Constraint
{
    public string $type;

    public bool $caseSensitive = true;

    public string $message = 'This value is key-valueed';
}
