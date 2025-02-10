<?php

namespace App\Repository;

use App\Entity\Pokedex;
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
        ->setParameter('user', $user)
        ->orderBy('p.pokemon', 'ASC')
        ->getQuery()
        ->getResult();
}

}
