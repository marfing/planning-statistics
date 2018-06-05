<?php

namespace App\Repository;

use App\Entity\StatisticaRete;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StatisticaRete|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatisticaRete|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatisticaRete[]    findAll()
 * @method StatisticaRete[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatisticaReteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StatisticaRete::class);
    }

//    /**
//     * @return StatisticaRete[] Returns an array of StatisticaRete objects
//     */
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
    public function findOneBySomeField($value): ?StatisticaRete
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
