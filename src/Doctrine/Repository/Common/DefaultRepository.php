<?php declare(strict_types=1);
namespace App\Doctrine\Repository\Common;

use BadMethodCallException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

/**
 * @template T of object
 * @template-extends ServiceEntityRepository<T>
 */
abstract class DefaultRepository extends ServiceEntityRepository
{
    public const ENTITY_CLASS = null;

    protected ManagerRegistry $registry;
    protected LoggerInterface $logger;

    public function __construct(
        ManagerRegistry $registry,
        LoggerInterface $logger
    )
    {
        $this->registry = $registry;
        $this->logger = $logger;
        parent::__construct(
            $registry,
            static::ENTITY_CLASS ?? throw new BadMethodCallException(static::class . "::ENTITY_CLASS is not defined")
        );
    }

    /**
     * @inheritDoc
     */
    public function findAll(array $order_by = null, string $assoc_by_property = null): array
    {
        return $this->findBy([], $order_by, null, null, $assoc_by_property);
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, string $assoc_by_property = null): array
    {
        $entities = parent::findBy($criteria, $orderBy, $limit, $offset);
        if($assoc_by_property === null || !$entities){
            return $entities;
        }

        $associated = [];
        foreach($entities as $idx => $entity){
            if(!$idx && !property_exists($entity, $assoc_by_property)){
                throw new InvalidArgumentException("Property " . get_class($entity) . "::{$assoc_by_property} not found");
            }
            $associated[$entity->{$assoc_by_property}] = $entity;
        }
        return $associated;
    }


    public function save(object $entity, bool $flush_immediately = true): void
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        if($flush_immediately){
            $em->flush();
        }
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        $em = parent::getEntityManager();
        if(!$em->isOpen()){
            $this->logger->warning("Warning, entity manager is closed! Resetting...", [
                'entity_class' => static::ENTITY_CLASS,
                'repository_class' => get_class($this)
            ]);
            $this->registry->resetManager();
        }
        return parent::getEntityManager();
    }

    public function saveMultiple(iterable $entities, bool $flush_immediately = true, bool $in_transaction = false): int
    {
        $saved = 0;
        $em = $this->getEntityManager();

        foreach($entities as $entity){
            $em->persist($entity);
            $saved++;
        }

        if(!$saved || !$flush_immediately){
            return $saved;
        }

        if($in_transaction){
            $em->beginTransaction();
            $em->flush();
            $em->commit();
        } else {
            $em->flush();
        }

        return $saved;
    }


    public function remove(object $entity, bool $flush_immediately = true): void
    {
        $em = $this->getEntityManager();
        $em->remove($entity);
        if($flush_immediately){
            $em->flush();
        }
    }


    public function removeMultiple(iterable $entities, bool $flush_immediately = true, bool $in_transaction = false): int
    {
        $removed = 0;
        $em = $this->getEntityManager();
        if($flush_immediately && $in_transaction){
            $em->beginTransaction();
        }

        foreach($entities as $entity){
            $em->remove($entity);
            $removed++;
        }

        if(!$flush_immediately){
            return $removed;
        }

        $em->flush();
        if($in_transaction){
            $em->commit();
        }

        return $removed;
    }
}
