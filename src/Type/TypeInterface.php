<?php

declare(strict_types=1);

namespace Survos\KeyValueBundle\Type;

use Survos\KeyValueBundle\Entity\KeyValueManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

interface TypeInterface
{
    /**
     * Describes if type supported by current class.
     */
    public function supports(string $type): bool;

    /**
     * Checks if value is satisfied by current type.
     */
    public function satisfies(string $value): bool;

    /**
     * Validates current value.
     * Used to implement custom storage validator.
     */
    public function validate(
        string $value,
        Constraint $constraint,
        ExecutionContextInterface &$context,
        KeyValueManagerInterface $manager,
    ): void;
}
