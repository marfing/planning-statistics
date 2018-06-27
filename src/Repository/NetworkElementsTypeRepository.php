<?php

namespace App\Repository;

use App\Entity\NetworkElementsType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method NetworkElementsType|null find($id, $lockMode = null, $lockVersion = null)
 * @method NetworkElementsType|null findOneBy(array $criteria, array $orderBy = null)
 * @method NetworkElementsType[]    findAll()
 * @method NetworkElementsType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NetworkElementsTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NetworkElementsType::class);
    }

//    /**
//     * @return NetworkElementsType[] Returns an array of NetworkElementsType objects
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
    public function findOneBySomeField($value): ?NetworkElementsType
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
