<?php

declare(strict_types=1);

namespace Survos\KeyValueBundle\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Survos\KeyValueBundle\Repository\KeyValueRepository;

class KeyValueManager implements KeyValueManagerInterface
{
    private KeyValueRepository $keyValueRepository;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private array $config = [],
        private ?string $defaultList = null,
    ) {
        $this->keyValueRepository = $this->em->getRepository(KeyValue::class);
    }

    public function getDefaultList(bool $throwErrorIfMissing = true): ?string
    {
        if ($throwErrorIfMissing && !$this->defaultList) {
            throw new \LogicException('Either configure default_list or explicitly pass the list name.');
        }

        return $this->defaultList;
    }

    public function setDefaultList(string $defaultList): self
    {
        $this->defaultList = $defaultList;

        return $this;
    }

    public function has(string $value, ?string $type = null, bool $isCaseSensitive = true): bool
    {
        $type ??= $this->getDefaultList();

        return $this->keyValueRepository->matchValue($value, $type, $isCaseSensitive);
    }

    private function isStrict(): bool
    {
        return ($this->config['strict'] ?? false) && [] !== ($this->config['lists'] ?? []);
    }

    public function remove(string $value, ?string $type = null, bool $flush = true): void
    {
        $type ??= $this->getDefaultList();

        if (!$this->has($value, $type)) {
            throw new \LogicException("Value '$value' does not exist");
        }

        if ($entity = $this->keyValueRepository->findOneBy([
            'value' => $value,
            'type' => $type,
        ])) {
            $this->em->remove($entity);
            if ($flush) {
                $this->em->flush();
            }
        }
    }

    public function add(string $value, ?string $type = null, bool $flush = true): void
    {
        $type ??= $this->getDefaultList();

        if ($this->isStrict()) {
            $validLists = array_map(static fn(array $config): string => $config['name'], $this->config['lists']);
            if (!in_array($type, $validLists, true)) {
                throw new \LogicException(sprintf("Type '%s' is not allowed: %s", $type, implode(', ', $validLists)));
            }
        }

        if ($this->has($value, $type)) {
            throw new \LogicException("Value '$value' already exists");
        }

        $this->persist($value, $type);

        if ($flush) {
            $this->em->flush();
        }
    }

    /** {@inheritDoc} */
    public function getList(?string $type = null): array
    {
        $type ??= $this->getDefaultList();

        return $this->keyValueRepository->createQueryBuilder('kv')
            ->select(['kv.value'])
            ->andWhere('kv.type = :type')
            ->setParameter('type', $type)
            ->orderBy('kv.value', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getTypes(): array
    {
        return $this->keyValueRepository->createQueryBuilder('kv')
            ->select('kv.type, count(kv.value) as count')
            ->groupBy('kv.type')
            ->orderBy('kv.type', 'ASC')
            ->getQuery()
            ->getResult();
    }

    private function persist(string $value, string $type): void
    {
        $this->em->persist(new KeyValue($value, $type));
    }
}
