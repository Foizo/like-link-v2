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

#[ORM\Index(columns: ['generated_shortcut', 'customer_shortcut'], name: 'generated_customer_shortcut_url_idx')]

#[UniqueEntity(fields: ['generated_shortcut', 'customer_shortcut'], message: 'Generated and customer shortcut on given domain already exist.')]
class ShortcutUrl extends ObjectWithoutMagicAccess
{
    use AutoIncrementIdTrait;
    use RelatedDomainTrait;

    #[ORM\Column(length: GeneratedShortcut::ENTITY_COLUMN_LENGTH)]
    #[Assert\Regex(GeneratedShortcut::SHORTCUT_PATTERN)]
    #[Assert\NotBlank]
    public GeneratedShortcut|string $generated_shortcut = GeneratedShortcut::NOT_GENERATED;

    #[ORM\Column(length: CustomerShortcut::ENTITY_COLUMN_LENGTH)]
    #[Assert\Regex(CustomerShortcut::SHORTCUT_PATTERN)]
    #[Assert\NotBlank]
    public CustomerShortcut|string $customer_shortcut = CustomerShortcut::NOT_SPECIFIED;

    /** @see CustomerUrl::$shortcut_url */
    #[ORM\OneToOne(mappedBy: 'shorted_url')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    public CustomerUrl $customer_url;
}
