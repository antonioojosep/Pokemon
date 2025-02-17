<?php

namespace App\Repository;

use App\Entity\Batalla;
use App\Entity\BatallaEquipos;
use App\Entity\Pokedex;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pokedex>
 */
class PokedexRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokedex::class);
    }

    public function findByUser($user)
    {
    return $this->createQueryBuilder('p')
        ->andWhere('p.user = :user')
        ->andWhere('p.pokemon IS NOT NULL')
        ->setParameter('user', $user)
        ->orderBy('p.pokemon', 'ASC')
        ->getQuery()
        ->getResult();
    }

    public function findByUserNoDerrotado($user)
    {
    return $this->createQueryBuilder('p')
        ->andWhere('p.user = :user')
        ->andWhere('p.pokemon IS NOT NULL')
        ->andWhere('p.derrotado = 0')
        ->setParameter('user', $user)
        ->orderBy('p.pokemon', 'ASC')
        ->getQuery()
        ->getResult();
    }
}
