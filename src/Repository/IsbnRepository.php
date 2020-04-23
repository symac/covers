<?php

namespace App\Repository;

use App\Entity\Isbn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Isbn|null find($id, $lockMode = null, $lockVersion = null)
 * @method Isbn|null findOneBy(array $criteria, array $orderBy = null)
 * @method Isbn[]    findAll()
 * @method Isbn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IsbnRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Isbn::class);
    }

    // /**
    //  * @return Isbn[] Returns an array of Isbn objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Isbn
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
