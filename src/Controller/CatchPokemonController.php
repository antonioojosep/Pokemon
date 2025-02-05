<?php

namespace App\Controller;

use App\Entity\Pokedex;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatchPokemonController extends AbstractController
{
    #[Route('/catch-pokemon/{id}', name: 'app_catch_pokemon')]
    public function catch(int $id, PokemonRepository $pokemonRepository, EntityManagerInterface $entityManager): Response
    {
        $pokemon = $pokemonRepository->find($id);
        $success = (random_int(1, 100) <= 60);

        if ($success) {
            $pokedex = new Pokedex();
            $pokedex->setPokemon($pokemon);
            $pokedex->setUser($this->getUser());
            $pokedex->setNivel(1);
            $pokedex->setFuerza(10);
            
            $entityManager->persist($pokedex);
            $entityManager->flush();
        }

        return $this->render('catch_pokemon/index.html.twig', [
            'pokemon' => $pokemon,
            'success' => $success
        ]);
    }
} 