<?php

declare(strict_types=1);


namespace Survos\KeyValueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Survos\KeyValueBundle\Repository\KeyValueRepository;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: KeyValueRepository::class)]
#[ORM\Table]
#[ORM\UniqueConstraint(name: 'kv_type_value', columns: ['type', 'value'])]
class KeyValue implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    public function __construct(
        #[ORM\Column(type: 'string', length: 1024, nullable: false)]
        #[Assert\NotBlank]
        protected string $value,

        #[ORM\Column(type: 'string', length: 255, nullable: false)]
        #[Assert\NotBlank]
        protected string $type,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function __toString(): string
    {
        return sprintf('%s/%s', $this->type, $this->value);
    }
}
