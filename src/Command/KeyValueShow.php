<?php declare(strict_types=1);

namespace Survos\KeyValueBundle\Command;

use Survos\KeyValueBundle\Entity\KeyValue;
use Survos\KeyValueBundle\Entity\KeyValueManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('survos:key-value:show', 'List kv entities by list name', aliases: ['kv:show'])]
class KeyValueShow extends BaseKeyValue
{
    protected function configure(): void
    {
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string|null $type */
        $type = $input->getArgument('type');
        $style = new SymfonyStyle($input, $output);
        if ($type) {
            $list = $this->kvManager->getList($type);
            $style->table([$type], array_map(fn($item) => [$item], $list));
        } else {
            $list = $this->kvManager->getTypes();
            $style->table(['type', 'count'], $list);
        }

        if (!$list) {
            $style->success("No entries found");
        }


        return self::SUCCESS;
    }
}
