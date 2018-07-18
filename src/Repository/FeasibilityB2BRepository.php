<?php

namespace App\Repository;

use App\Entity\FeasibilityB2B;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FeasibilityB2B|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeasibilityB2B|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeasibilityB2B[]    findAll()
 * @method FeasibilityB2B[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeasibilityB2BRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FeasibilityB2B::class);
    }

//    /**
//     * @return FeasibilityB2B[] Returns an array of FeasibilityB2B objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FeasibilityB2B
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByUserID($id)
    {
        return $this->createQueryBuilder('f')
            ->join('f.User', 'u')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
    }

}
