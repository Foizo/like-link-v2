<?php declare(strict_types=1);
namespace App\Doctrine\Entity\AppDomain\Parts;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class AppDomainStatistics
{
    #[ORM\Column(options: ['default' => 0])]
    public int $urls_count = 0;

    #[ORM\Column(options: ['default' => 0])]
    public int $redirects_count = 0;
}