<?php

namespace App\Repository;

use App\Entity\Isbn;
use App\Service\IsbnLibrary;
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
    private $isbnLibrary;
    public function __construct(ManagerRegistry $registry, IsbnLibrary $isbnLibrary)
    {
        $this->isbnLibrary = $isbnLibrary;
        parent::__construct($registry, Isbn::class);
    }

    public function findByValue(string $isbn) {
        $isbn = $this->isbnLibrary->clean($isbn);
        $result = $this->createQueryBuilder("i");
        if (strlen($isbn) == 10) {
            $result = $result->where("i.value10 = :isbn");
        } elseif (strlen($isbn) == 13) {
            $result = $result->where("i.value13 = :isbn");
        } else {
            return null;
        }
        $result = $result->setParameter("isbn", $isbn)
            ->getQuery()
            ->getOneOrNullResult();

        return $result;

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
