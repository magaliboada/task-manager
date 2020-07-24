<?php

namespace App\Repository;

use App\Entity\Lapse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lapse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lapse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lapse[]    findAll()
 * @method Lapse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LapseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lapse::class);
    }

    // /**
    //  * @return Lapse[] Returns an array of Lapse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lapse
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
