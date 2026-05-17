<?php

declare(strict_types=1);

namespace Survos\KeyValueBundle\Repository;

use Doctrine\ORM\EntityRepository;

class KeyValueRepository extends EntityRepository
{
    /** @codeCoverageIgnore */
    public function matchValue(string $value, string $type, bool $isCaseSensitive = true): bool
    {
        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.type = :type')
            ->setParameter('type', $type)
            ->setMaxResults(1);

        if ($isCaseSensitive) {
            $qb
                ->andWhere('t.value = :value')
                ->setParameter('value', $value);
        } else {
            $qb
                ->andWhere('LOWER(t.value) = LOWER(:value)')
                ->setParameter('value', $value);
        }

        return (int) $qb
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}
