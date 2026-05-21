<?php

declare(strict_types=1);

namespace Survos\KeyValueBundle\Service;

use Survos\KeyValueBundle\Entity\KeyValueManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

final class KeyValueService
{
    public function __construct(private readonly KeyValueManagerInterface $kvManager) {}

    #[AsCommand('survos:kv:add', 'Add a value to a key/value list', ['kv:add', 'survos:key-value:add'])]
    public function add(
        SymfonyStyle $io,
        #[Argument('Value to add')] string $value,
        #[Argument('KeyValue type, e.g. "email"')] ?string $type = null,
    ): int {
        $type ??= $this->kvManager->getDefaultList();
        $this->kvManager->add($value, $type);
        $io->success("Added $type $value");

        return Command::SUCCESS;
    }

    #[AsCommand('survos:kv:remove', 'Remove a value from a key/value list', ['kv:remove', 'survos:key-value:remove'])]
    public function remove(
        SymfonyStyle $io,
        #[Argument('Value to remove')] string $value,
        #[Argument('KeyValue type, e.g. "email"')] ?string $type = null,
    ): int {
        $type ??= $this->kvManager->getDefaultList();

        if (!$this->kvManager->has($value, $type)) {
            $io->warning('Key value "' . $type . '/' . $value . '" does not exist.');
            return Command::SUCCESS;
        }

        $this->kvManager->remove(value: $value, type: $type);
        $io->success("Removed $type $value");

        return Command::SUCCESS;
    }

    #[AsCommand('survos:kv:show', 'List key/value entries by type', ['kv:show', 'survos:key-value:show'])]
    public function show(
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
