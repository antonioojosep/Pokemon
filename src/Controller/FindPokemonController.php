<?php

namespace App\Controller;

use App\Repository\PokemonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FindPokemonController extends AbstractController
{
    #[Route('/find-pokemon', name: 'findPokemon')]
    public function index(PokemonRepository $pokemonRepository): Response
    {
        $randomPokemon = $pokemonRepository->getRandomPokemon();

        return $this->render('find_pokemon/index.html.twig', [
            'pokemon' => $randomPokemon,
        ]);
    }
} 