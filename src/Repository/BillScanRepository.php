<?php

namespace App\Repository;

use App\Entity\BillScan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BillScan|null find($id, $lockMode = null, $lockVersion = null)
 * @method BillScan|null findOneBy(array $criteria, array $orderBy = null)
 * @method BillScan[]    findAll()
 * @method BillScan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BillScanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BillScan::class);
    }

    // /**
    //  * @return BillScan[] Returns an array of BillScan objects
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
    public function findOneBySomeField($value): ?BillScan
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
