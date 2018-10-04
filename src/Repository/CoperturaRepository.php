<?php

namespace App\Repository;

use App\Entity\Copertura;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Copertura|null find($id, $lockMode = null, $lockVersion = null)
 * @method Copertura|null findOneBy(array $criteria, array $orderBy = null)
 * @method Copertura[]    findAll()
 * @method Copertura[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoperturaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Copertura::class);
    }

//    /**
//     * @return Copertura[] Returns an array of Copertura objects
//     */
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
    public function findOneBySomeField($value): ?Copertura
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
