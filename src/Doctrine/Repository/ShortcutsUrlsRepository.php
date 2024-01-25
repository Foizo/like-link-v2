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

    function findOneByGeneratedOrCustomerShortcut(AppDomain $appDomain, string $shortcut, bool $allow_cache = true): ?ShortcutUrl
    {
        $qb = $this->createQueryBuilder('short')
            ->where('short.short.app_domain = :app_domain')
            ->andWhere('short.short.generated_shortcut = :shortcut OR short.customer_shortcut = :shortcut')
            ->join('shor.customer_url', 'url')
            ->select('short, url')
            ->setParameters([
                'app_domain' => $appDomain,
                'shortcut' => $shortcut
            ])
            ->getQuery();

        if ($allow_cache) {
            $qb->enableResultCache(self::CACHE_TTL, $appDomain->identifier . '-' . self::CACHE_KEY_PREFIX . $shortcut);
        }

        return $qb->getOneOrNullResult();
    }
}
