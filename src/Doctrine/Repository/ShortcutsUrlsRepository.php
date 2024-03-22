<?php declare(strict_types=1);
namespace App\Doctrine\Repository;

use App\Doctrine\Entity\AppDomain;
use App\Doctrine\Entity\ShortcutUrl;
use App\Doctrine\Repository\Common\DefaultRepository;

/** @extends DefaultRepository<ShortcutUrl> */
class ShortcutsUrlsRepository extends DefaultRepository
{
    public const ENTITY_CLASS = ShortcutUrl::class;
    const CACHE_TTL = 3600;
    const CACHE_KEY_PREFIX = 'shortcut-';

    function findOneByGeneratedOrCustomerShortcut(AppDomain $appDomain, string $shortcut): ?ShortcutUrl
    {
        return $this->createQueryBuilder('short')
            ->where('short.app_domain = :app_domain')
            ->andWhere('short.generated_shortcut = :shortcut OR short.customer_shortcut = :shortcut')
            ->join('short.customer_url', 'url')
            ->select('short, url')
            ->setParameters([
                'app_domain' => $appDomain,
                'shortcut' => $shortcut
            ])
            ->getQuery()->getOneOrNullResult();
    }
}
