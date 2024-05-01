<?php declare(strict_types=1);
namespace App\Doctrine\Entity;

use App\Doctrine\Entity\Common\DefaultEntity;
use App\Doctrine\Entity\Common\Traits\RelatedDomainTrait;
use App\Doctrine\Entity\CustomerUrl\Parts\CustomerUrlParameters;
use App\Doctrine\Entity\CustomerUrl\Parts\CustomerUrlShortcuts;
use App\Doctrine\Entity\CustomerUrl\Parts\CustomerUrlStatistics;
use App\Doctrine\Repository\CustomerUrlsRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerUrlsRepository::class)]
#[ORM\Table(name: 'customer_urls')]

#[ORM\UniqueConstraint(name: 'generated_shortcut', columns: ['shortcuts_generated_shortcut'])]
#[ORM\UniqueConstraint(name: 'customer_shortcut', columns: ['shortcuts_customer_shortcut'])]
#[ORM\UniqueConstraint(name: 'generated_vs_customer_shortcut', columns: ['shortcuts_generated_shortcut', 'shortcuts_customer_shortcut'])]

#[ORM\Index(columns: ['destination_url_md5_hash'], name: 'customer_url_destination_hash_idx')]
#[ORM\Index(columns: ['created_date'], name: 'customer_url_created_date_idx')]
class CustomerUrl extends DefaultEntity
{
    use RelatedDomainTrait;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Url]
    #[Assert\NotBlank]
    public string $destination_url;

    #[ORM\Column(length: 32)]
    #[Assert\NotBlank]
    public string $destination_url_md5_hash;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public DateTime $created_date;

    #[ORM\Embedded]
    public CustomerUrlParameters $params;

    #[ORM\Embedded]
    public CustomerUrlStatistics $stats;

    #[ORM\Embedded]
    public CustomerUrlShortcuts $shortcuts;


    function __construct()
    {
        parent::__construct();
        $this->created_date = new DateTime();
        $this->params = new CustomerUrlParameters();
        $this->stats = new CustomerUrlStatistics();
        $this->shortcuts = new CustomerUrlShortcuts();
    }
}