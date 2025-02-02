<?php

namespace App\Repository;

use App\Entity\Tracked;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tracked>
 *
 * @method Tracked|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tracked|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tracked[]    findAll()
 * @method Tracked[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tracked::class);
    }

//    /**
//     * @return Tracked[] Returns an array of Tracked objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tracked
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
