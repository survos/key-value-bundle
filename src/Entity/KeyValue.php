<?php

declare(strict_types=1);

namespace Survos\KeyValueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Survos\FieldBundle\Attribute\EntityMeta;
use Survos\KeyValueBundle\Repository\KeyValueRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[EntityMeta(icon: 'mdi:format-list-bulleted', group: 'Key Value')]
#[ORM\Entity(repositoryClass: KeyValueRepository::class)]
#[ORM\Table]
#[ORM\UniqueConstraint(name: 'kv_type_value', columns: ['type', 'value'])]
final class KeyValue implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    public function __construct(
        #[ORM\Column(type: 'string', length: 1024, nullable: false)]
        #[Assert\NotBlank]
        public readonly string $value,

        #[ORM\Column(type: 'string', length: 255, nullable: false)]
        #[Assert\NotBlank]
        public readonly string $type,
    ) {}

    public function __toString(): string
    {
        return sprintf('%s/%s', $this->type, $this->value);
    }
}
