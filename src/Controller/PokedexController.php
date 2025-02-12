<?php

namespace App\Controller;

use App\Entity\Pokedex;
use App\Form\PokedexType;
use App\Repository\BatallaRepository;
use App\Repository\PokedexRepository;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/pokedex')]
final class PokedexController extends AbstractController
{
    #[Route(name: 'app_pokedex_index', methods: ['GET'])]
    public function index(PokedexRepository $pokedexRepository): Response
    {
        return $this->render('pokedex/index.html.twig', [
            'pokemons' => $pokedexRepository->findByUser($this->getUser()),
            'modo' => 'pokedex'
        ]);
    }

    #[Route('/entrenar/:{id}', name: 'app_pokedex_entrenar', methods: ['GET', 'POST'])]
    public function entrenar(int $id, PokedexRepository $pokedexRepository, EntityManagerInterface $entityManager) : Response
    {
        $pokemon = $pokedexRepository->findOneBy(['id' => $id]);
        $pokemon->entrenar();
        $entityManager->persist($pokemon);
        $entityManager->flush();

        return $this->redirectToRoute('app_pokedex_index');
    }

    #[Route('/lucharRandom/:{id}', name: 'app_pokedex_luchar_random', methods: ['GET'])]
    public function lucharRandom(int $id, BatallaRepository $batallaRepository, PokedexRepository $pokedexRepository , PokemonRepository $pokemonRepository,EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $myPokemon = $pokedexRepository->findOneBy(['id' => $id]);
        $batalla = $batallaRepository->randomBattle($user, $myPokemon, $pokemonRepository);
        

        $entityManager->persist($myPokemon, $batalla);
        $entityManager->flush();

        return $this->render('batalla/show.html.twig', [
            'pokemon2' => $batalla->getPokemon2(),
            'pokemon1' => $batalla->getPokemon1(),
            'ganador' => $batalla->getGanador()
        ]);
    }
}
