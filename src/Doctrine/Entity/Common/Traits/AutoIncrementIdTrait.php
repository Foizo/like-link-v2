<?php declare(strict_types=1);
namespace App\Doctrine\Entity\Common\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AutoIncrementIdTrait
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    public ?int $id = null;
}
