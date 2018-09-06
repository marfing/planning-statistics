<?php

namespace App\Repository;

use App\Entity\TrafficReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TrafficReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrafficReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrafficReport[]    findAll()
 * @method TrafficReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrafficReportRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TrafficReport::class);
    }

//    /**
//     * @return TrafficReport[] Returns an array of TrafficReport objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrafficReport
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}