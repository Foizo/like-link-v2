<?php declare(strict_types=1);
namespace App\Doctrine\Entity;

use App\Doctrine\Entity\AppDomain\Parts\AppDomainStatistics;
use App\Doctrine\Entity\Common\DefaultEntity;
use App\Doctrine\Repository\AppDomainsRepository;
use App\Enums\Domain;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AppDomainsRepository::class)]
#[ORM\Table(name: 'app_domains')]

#[UniqueEntity(fields: 'identifier', message: 'Given identifier already exist.')]
#[UniqueEntity(fields: 'domain', message: 'Given domain already exist.')]
class AppDomain extends DefaultEntity
{
    #[ORM\Column(length: 10)]
    #[Assert\Regex('~^[a-z\-]+$~')]
    #[Assert\NotBlank]
    public string $identifier;

    #[ORM\Column(length: Domain::ENTITY_DOMAIN_COLUMN_LENGTH)]
    #[Assert\Regex(Domain::APP_DOMAIN_PATTERN)]
    #[Assert\NotBlank]
    public string $domain;

    #[ORM\Column(length: Domain::ENTITY_LANG_COLUMN_LENGTH)]
    #[Assert\NotBlank]
    public Domain $language = Domain::DEFAULT_APP_LANGUAGE;

    #[ORM\Column]
    #[Assert\Email]
    #[Assert\NotBlank]
    public string $contact_email;

    #[ORM\Embedded]
    public AppDomainStatistics $stats;

    function __construct()
    {
        parent::__construct();
        $this->stats = new AppDomainStatistics();
    }
}
