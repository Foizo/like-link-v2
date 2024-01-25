<?php declare(strict_types=1);
namespace App\Doctrine\Entity\Common\Traits;

use App\Doctrine\Entity\AppDomain;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait RelatedDomainTrait
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    public AppDomain $app_domain;
}
