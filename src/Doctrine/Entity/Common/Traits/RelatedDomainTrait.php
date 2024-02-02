<?php declare(strict_types=1);
namespace App\Doctrine\Entity\Common\Traits;

use App\Doctrine\Entity\AppDomain;
use Doctrine\ORM\Mapping as ORM;

trait RelatedDomainTrait
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    public AppDomain $app_domain;
}
