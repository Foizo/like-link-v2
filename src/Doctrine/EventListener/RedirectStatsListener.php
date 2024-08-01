<?php declare(strict_types=1);
namespace App\Doctrine\EventListener;

use App\Doctrine\Entity\AppDomain;
use App\Doctrine\Entity\CustomerUrl;
use App\Doctrine\Entity\CustomerUrl\CustomerUrlRedirect;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Query\Parameter;

#[AsDoctrineListener(event: Events::postPersist)]
class RedirectStatsListener
{
    function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof CustomerUrlRedirect) {
            $em = $args->getObjectManager();

            if ($entity->customer_url) {
                $em->createQueryBuilder()
                    ->update(CustomerUrl::class, 'cu')
                    ->set('cu.stats.redirects_count', 'cu.stats.redirects_count + 1')
                    ->set('cu.stats.last_redirect_date', ':date')
                    ->where('cu = :customer_url')
                    ->setParameters(new ArrayCollection([
                        new Parameter('date', new DateTime()),
                        new Parameter('customer_url', $entity->customer_url)
                    ]))
                    ->getQuery()
                    ->execute();
            }

            $em->createQueryBuilder()
                ->update(AppDomain::class, 'ad')
                ->set('ad.stats.redirects_count', 'ad.stats.redirects_count + 1')
                ->where('ad = :app_domain')
                ->setParameter('app_domain', $entity->app_domain)
                ->getQuery()
                ->execute();
        }
    }
}