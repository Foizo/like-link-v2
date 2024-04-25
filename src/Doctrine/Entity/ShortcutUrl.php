<?php declare(strict_types=1);
namespace App\Doctrine\Entity;

use App\Doctrine\Entity\Common\Traits\AutoIncrementIdTrait;
use App\Doctrine\Entity\Common\Traits\RelatedDomainTrait;
use App\Doctrine\Repository\ShortcutsUrlsRepository;
use App\Enums\Shortcut\GeneratedShortcut;
use App\Enums\Shortcut\CustomerShortcut;
use App\SharedModels\ObjectWithoutMagicAccess\ObjectWithoutMagicAccess;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ShortcutsUrlsRepository::class)]
#[ORM\Table(name: 'shortcuts_urls')]

#[ORM\UniqueConstraint(name: 'generated_customer_shortcut', columns: ['generated_shortcut', 'customer_shortcut'])]

#[UniqueEntity(fields: ['generated_shortcut', 'customer_shortcut'], message: 'Generated and customer shortcut on given domain already exist.')]
class ShortcutUrl extends ObjectWithoutMagicAccess
{
    use AutoIncrementIdTrait;
    use RelatedDomainTrait;

    #[ORM\Column(
        length: GeneratedShortcut::ENTITY_COLUMN_LENGTH,
        options: [
            'collation' => 'utf8_bin',
            'default' => GeneratedShortcut::NOT_GENERATED
        ]
    )]
    #[Assert\Regex(GeneratedShortcut::SHORTCUT_PATTERN)]
    #[Assert\NotBlank]
    public string $generated_shortcut = GeneratedShortcut::NOT_GENERATED;

    #[ORM\Column(
        length: CustomerShortcut::ENTITY_COLUMN_LENGTH,
        options: [
            'collation' => 'utf8_bin',
            'default' => CustomerShortcut::NOT_SPECIFIED
        ]
    )]
    #[Assert\Regex(CustomerShortcut::SHORTCUT_PATTERN)]
    #[Assert\NotBlank]
    public string $customer_shortcut = CustomerShortcut::NOT_SPECIFIED;

    /** @see CustomerUrl::$shortcut_url */
    #[ORM\OneToOne(mappedBy: 'shortcut_url')]
    public CustomerUrl $customer_url;
}
