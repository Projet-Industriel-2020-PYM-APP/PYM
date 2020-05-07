<?php

namespace App\Repository;

use App\Entity\ServiceCategorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceCategorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceCategorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceCategorie[]    findAll()
 * @method ServiceCategorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceCategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceCategorie::class);
    }

    // /**
    //  * @return ServiceCategorie[] Returns an array of ServiceCategorie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ServiceCategorie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
