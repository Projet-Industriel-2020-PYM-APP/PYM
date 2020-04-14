<?php

namespace App\Repository;

use App\Entity\BatimentPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BatimentPosition|null find($id, $lockMode = null, $lockVersion = null)
 * @method BatimentPosition|null findOneBy(array $criteria, array $orderBy = null)
 * @method BatimentPosition[]    findAll()
 * @method BatimentPosition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BatimentPositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BatimentPosition::class);
    }

    // /**
    //  * @return BatimentPosition[] Returns an array of BatimentPosition objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BatimentPosition
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
