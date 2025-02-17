<?php

namespace App\Repository;

use App\Entity\BatallaEquipos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BatallaEquipos>
 */
class BatallaEquiposRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BatallaEquipos::class);
    }

    //    /**
    //     * @return BatallaEquipos[] Returns an array of BatallaEquipos objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?BatallaEquipos
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    
    public function getAllPokemons1()
    {
        return $this->createQueryBuilder('b')
            ->select('p')
            ->from('App:Pokemon', 'p')
            ->innerJoin('b.pokemon1', 'p1')
            ->where('p1.id = b.pokemon1')
            ->getQuery()
            ->getResult();
    }
}
