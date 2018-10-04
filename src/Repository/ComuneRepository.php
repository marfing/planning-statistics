<?php

namespace App\Repository;

use App\Entity\Comune;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Comune|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comune|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comune[]    findAll()
 * @method Comune[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComuneRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comune::class);
    }

//    /**
//     * @return Comune[] Returns an array of Comune objects
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
    public function findOneBySomeField($value): ?Comune
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
