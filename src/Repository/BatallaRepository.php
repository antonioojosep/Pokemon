<?php

namespace App\Repository;

use App\Entity\Batalla;
use App\Entity\Pokedex;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Batalla>
 */
class BatallaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Batalla::class);
    }

    public function randomBattle(?User $user1, ?Pokedex $pokemon1, PokemonRepository $pokemonRepository, EntityManager $entityManager): Batalla
    {
        // Pokemon Aleatorio
        $randomPokemon = $pokemonRepository->getRandomPokemon();
        $pokemon2 = new Pokedex();
        $pokemon2->setPokemon($randomPokemon);
        $pokemon2->setFuerza(rand(10, $pokemon1->getFuerza()*2));
        $pokemon2->setNivel(rand(1, $pokemon1->getNivel()*2));

        // Batalla
        $batalla = new Batalla();
        $batalla->setUser1($user1);
        $batalla->setPokemon2($pokemon2);
        $batalla->setPokemon1($pokemon1);

        if (($pokemon1->getFuerza() * $pokemon1->getNivel()) >= ($pokemon2->getFuerza() * $pokemon2->getNivel())) {
            $batalla->setGanador($user1);
            $pokemon1->gana();
        } else {
            $pokemon1->pierde();
        }
        $entityManager->persist($pokemon1);
        $entityManager->persist($pokemon2);
        $entityManager->flush();
        return $batalla;
    }

    public function createBattle(?User $user1, ?Pokedex $pokemon1, ?User $user2, ?Pokedex $pokemon2): ?Batalla
{
    $batalla = new Batalla();
    $batalla->setUser1($user1);
    $batalla->setUser2($user2);
    $batalla->setPokemon1($pokemon1);
    $batalla->setPokemon2($pokemon2);

    if (($pokemon1->getFuerza() * $pokemon1->getNivel()) >= ($pokemon2->getFuerza() * $pokemon2->getNivel())) {
        $batalla->setGanador($user1);
        $pokemon1->gana();
    } else {
        $batalla->setGanador($user2);
        $pokemon2->gana();
    }

    return $batalla;
}

    public function getBatallas() {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('App\Entity\BatallaEquipos', 'be', 'WITH', 
                'b.id = be.batalla1 OR b.id = be.batalla2 OR b.id = be.batalla3')
            ->where('be.id IS NULL');

        return $qb->getQuery()->getResult();
    }

}
