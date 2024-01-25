<?php declare(strict_types=1);
namespace App\Doctrine\Entity\Common;

use App\SharedModels\ObjectWithoutMagicAccess\ObjectWithoutMagicAccess;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Doctrine\Entity\Common\Traits\AutoIncrementIdTrait;
use App\Doctrine\Entity\Common\Traits\CreatedWhenTrait;
use App\Doctrine\Entity\Common\Traits\UpdatedWhenTrait;

#[ORM\MappedSuperclass]
abstract class DefaultEntity extends ObjectWithoutMagicAccess
{
    use AutoIncrementIdTrait;
    use CreatedWhenTrait;
    use UpdatedWhenTrait;

    public function __construct()
    {
        $this->created_when = new DateTime();
        $this->updated_when = new DateTime();
    }
}
