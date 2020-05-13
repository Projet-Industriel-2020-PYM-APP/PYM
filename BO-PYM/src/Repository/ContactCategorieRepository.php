<?php

namespace App\Repository;

use App\Entity\ContactCategorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactCategorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactCategorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactCategorie[]    findAll()
 * @method ContactCategorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactCategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactCategorie::class);
    }

    // /**
    //  * @return ContactCategorie[] Returns an array of ContactCategorie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ContactCategorie
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
