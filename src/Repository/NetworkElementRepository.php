<?php

namespace App\Repository;

use App\Entity\NetworkElement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method NetworkElement|null find($id, $lockMode = null, $lockVersion = null)
 * @method NetworkElement|null findOneBy(array $criteria, array $orderBy = null)
 * @method NetworkElement[]    findAll()
 * @method NetworkElement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NetworkElementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NetworkElement::class);
    }

//    /**
//     * @return NetworkElement[] Returns an array of NetworkElement objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NetworkElement
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
