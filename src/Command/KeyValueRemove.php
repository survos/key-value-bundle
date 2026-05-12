<?php

declare(strict_types=1);

namespace Survos\KeyValueBundle\Command;

use Survos\KeyValueBundle\Entity\KeyValueManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('survos:key-value:remove', 'Remove data from key/value storage', ['kv:remove'])]
final class KeyValueRemove
{
    public function __construct(private readonly KeyValueManagerInterface $kvManager)
    {
    }

    public function __invoke(
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
        $io->success("Deleted $type $value");

        return Command::SUCCESS;
    }
}
