<?php declare(strict_types=1);
namespace App\Doctrine\Repository;

use App\Doctrine\Entity\AppDomain;
use App\Doctrine\Entity\CustomerUrl;
use App\Doctrine\Repository\Common\DefaultRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

/** @extends DefaultRepository<CustomerUrl> */
class CustomerUrlsRepository extends DefaultRepository
{
    public const ENTITY_CLASS = CustomerUrl::class;

    function findOneByDestinationUrlHash(AppDomain $appDomain, string $destination_url_md5_hash): ?CustomerUrl
    {
        return $this->createQueryBuilder('url')
            ->where('url.app_domain = :app_domain')
            ->andWhere('url.destination_url_md5_hash = :url_hash')
            ->setParameters(new ArrayCollection([
                new Parameter('app_domain', $appDomain),
                new Parameter('url_hash', $destination_url_md5_hash)
            ]))->getQuery()->getOneOrNullResult();
    }

    function findOneByGeneratedOrCustomerShortcut(AppDomain $appDomain, string $shortcut): ?CustomerUrl
    {
        return $this->createQueryBuilder('url')
            ->where('url.app_domain = :app_domain')
            ->andWhere('url.shortcuts.customer_shortcut = :shortcut OR url.shortcuts.generated_shortcut = :shortcut')
            ->setParameters(new ArrayCollection([
                new Parameter('app_domain', $appDomain),
                new Parameter('shortcut', $shortcut)
            ]))->getQuery()->getOneOrNullResult();
    }
}