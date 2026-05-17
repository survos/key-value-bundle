<?php

declare(strict_types=1);

namespace Survos\KeyValueBundle\Entity;

interface KeyValueManagerInterface
{
    public function has(string $value, ?string $type = null, bool $isCaseSensitive = true): bool;

    public function add(string $value, ?string $type = null, bool $flush = true): void;

    public function remove(string $value, ?string $type = null, bool $flush = true): void;

    /** @return list<string> */
    public function getList(?string $type = null): array;

    /** @return list<array{type: string, count: int|string}> */
    public function getTypes(): array;

    public function getDefaultList(bool $throwErrorIfMissing = true): ?string;

    public function setDefaultList(string $defaultList): self;
}
