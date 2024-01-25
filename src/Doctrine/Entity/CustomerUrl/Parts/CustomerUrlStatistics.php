<?php declare(strict_types=1);
namespace App\Doctrine\Entity\CustomerUrl\Parts;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CustomerUrlStatistics
{
    #[ORM\Column(options: ['default' => 0])]
    public int $redirects_count = 0;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    public ?DateTime $last_redirect_date = null;
}
