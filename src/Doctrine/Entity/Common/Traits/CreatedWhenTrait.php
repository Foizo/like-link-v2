<?php declare(strict_types=1);
namespace App\Doctrine\Entity\Common\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as ORMExt;

trait CreatedWhenTrait
{
    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[ORMExt\Timestampable(on: 'create')]
    public DateTime $created_when;
}
