<?php declare(strict_types=1);
namespace App\Doctrine\Repository;

use App\Doctrine\Entity\AppDomain;
use App\Doctrine\Repository\Common\DefaultRepository;

/** @extends DefaultRepository<AppDomain> */
class AppDomainsRepository extends DefaultRepository
{
    public const ENTITY_CLASS = AppDomain::class;
    const CACHE_TTL = 3600;
    const CACHE_KEY_PREFIX = 'app-';

    function findByIdentifier(string $identifier, bool $allow_cache = true): ?AppDomain
    {
        if(!$allow_cache){
            return $this->findOneBy(['identifier' => $identifier]);
        }

        $qb = $this->createQueryBuilder('app')
            ->where('app.identifier = :identifier')
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->enableResultCache(self::CACHE_TTL, self::CACHE_KEY_PREFIX . $identifier);

        return $qb->getOneOrNullResult();
    }
}
