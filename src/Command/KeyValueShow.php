<?php

declare(strict_types=1);

namespace Survos\KeyValueBundle\Command;

use Survos\KeyValueBundle\Entity\KeyValueManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('survos:key-value:show', 'List kv entities by list name', ['kv:show', 'survos:kv:show'])]
final class KeyValueShow
{
    public function __construct(private readonly KeyValueManagerInterface $kvManager)
    {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('KeyValue type, e.g. "email"')] ?string $type = null,
    ): int {
        if ($type) {
            $list = $this->kvManager->getList($type);
            $io->table([$type], array_map(fn($item) => [$item], $list));
        } else {
            $list = $this->kvManager->getTypes();
            $io->table(['type', 'count'], $list);
        }

        if (!$list) {
            $io->success('No entries found');
        }

        return Command::SUCCESS;
    }
}
