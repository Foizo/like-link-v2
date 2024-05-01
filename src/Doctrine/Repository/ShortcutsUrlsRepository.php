<?php declare(strict_types=1);
namespace App\Doctrine\Repository;

use App\Doctrine\Entity\AppDomain;
use App\Doctrine\Entity\ShortcutUrl;
use App\Doctrine\Repository\Common\DefaultRepository;

/** @extends DefaultRepository<ShortcutUrl> */
class ShortcutsUrlsRepository extends DefaultRepository
{
    public const ENTITY_CLASS = ShortcutUrl::class;
}
