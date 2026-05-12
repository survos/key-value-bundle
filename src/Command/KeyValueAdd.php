<?php

declare(strict_types=1);

namespace Survos\KeyValueBundle\Command;

use Survos\KeyValueBundle\Entity\KeyValueManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('survos:key-value:add', 'Add data to key/value storage', ['kv:add'])]
final class KeyValueAdd
{
    public function __construct(private readonly KeyValueManagerInterface $kvManager)
    {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Value to add')] string $value,
        #[Argument('KeyValue type, e.g. "email"')] ?string $type = null,
    ): int {
        $type ??= $this->kvManager->getDefaultList();
        $this->kvManager->add($value, $type);
        $io->success("Added $type $value");

        return Command::SUCCESS;
    }
}
