<?php declare(strict_types=1);
namespace App\Doctrine\Repository;

use App\Doctrine\Entity\AppDomain;
use App\Doctrine\Entity\CustomerUrl;
use App\Doctrine\Repository\Common\DefaultRepository;

/** @extends DefaultRepository<CustomerUrl> */
class CustomerUrlsRepository extends DefaultRepository
{
    public const ENTITY_CLASS = CustomerUrl::class;
    const CACHE_TTL = 3600;
    const CACHE_KEY_PREFIX = 'url-';

    function findOneByDestinationUrlHash(AppDomain $appDomain, string $destination_url_md5_hash): ?CustomerUrl
    {
        $qb = $this->createQueryBuilder('url')
            ->where('url.app_domain = :app_domain')
            ->andWhere('url.destination_url_md5_hash = :url_hash')
            ->join('url.shortcut_url', 'short')
            ->select('url, short')
            ->setParameters([
                'app_domain' => $appDomain,
                'url_hash' => $destination_url_md5_hash
            ])
            ->getQuery();

        return $qb->getOneOrNullResult();
    }
}