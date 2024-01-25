<?php declare(strict_types=1);
namespace App\Doctrine\Entity\CustomerUrl\Parts;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class CustomerUrlParameters
{
    #[ORM\Column(length: 5)]
    #[Assert\NotBlank]
    public string $scheme;

    #[ORM\Column]
    #[Assert\NotBlank]
    public string $host;

    #[ORM\Column(nullable: true)]
    public ?int $port = null;

    #[ORM\Column(nullable: true)]
    public ?string $user = null;

    #[ORM\Column(nullable: true)]
    public ?string $pass = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $path = null;

    #[ORM\Column(type: Types::JSON)]
    public array $queries = [];

    #[ORM\Column(nullable: true)]
    public ?string $fragment = null;
}