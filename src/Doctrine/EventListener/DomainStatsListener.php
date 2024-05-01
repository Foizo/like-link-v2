<?php declare(strict_types=1);
namespace App\Doctrine\EventListener;

use App\Doctrine\Entity\AppDomain;
use App\Doctrine\Entity\CustomerUrl;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postPersist)]
class DomainStatsListener
{
    function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

//        if ($entity instanceof CustomerUrl) {
//            $em = $args->getObjectManager();
//            $em->createQueryBuilder()
//                ->update(AppDomain::class, 'ad')
//                ->set('ad.stats.urls_count', 'ad.stats.urls_count + 1')
//                ->where('ad = :app_domain')
//                ->setParameter('app_domain', $entity->app_domain)
//                ->getQuery()
//                ->execute();
//        }
    }
}
