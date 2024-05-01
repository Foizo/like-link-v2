<?php declare(strict_types=1);
namespace App\Doctrine\Entity\CustomerUrl\Parts;

use App\Enums\Shortcut\CustomerShortcut;
use App\Enums\Shortcut\GeneratedShortcut;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class CustomerUrlShortcuts
{
    #[ORM\Column(
        length: GeneratedShortcut::ENTITY_COLUMN_LENGTH,
        nullable: true,
        options: [
            'collation' => 'utf8_bin'
        ]
    )]
    #[Assert\Regex(GeneratedShortcut::SHORTCUT_PATTERN)]
    #[Assert\NotBlank]
    public ?string $generated_shortcut = null;

    #[ORM\Column(
        length: CustomerShortcut::ENTITY_COLUMN_LENGTH,
        nullable: true,
        options: [
            'collation' => 'utf8_bin'
        ]
    )]
    #[Assert\Regex(CustomerShortcut::SHORTCUT_PATTERN)]
    #[Assert\NotBlank]
    public ?string $customer_shortcut = null;
}