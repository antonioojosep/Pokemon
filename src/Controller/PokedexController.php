<?php

namespace App\Controller;

use App\Entity\Batalla;
use App\Entity\Pokedex;
use App\Form\PokedexType;
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

    #[Route('/luchar/:{id}', name: 'app_pokedex_luchar', methods: ['GET', 'POST'])]
    public function luchar(int $id, PokemonRepository $pokemonRepository, PokedexRepository $pokedexRepository, EntityManagerInterface $entityManager)
    {
        $randomPokemon = $pokemonRepository->getRandomPokemon();
        $myPokemon = $pokedexRepository->findOneBy(['id' => $id]);
        $fuerza = rand(0, $myPokemon->getFuerza() + 10);
        $nivel = rand(0, $myPokemon->getNivel() + 2);
        $batalla = new Batalla();
        $batalla->setPokemonAleatorio($randomPokemon);
        $batalla->setPokemonUsuario($myPokemon);

        if (($myPokemon->getFuerza() * $myPokemon->getNivel()) >= ($fuerza * $nivel)) {
            $batalla->setGanador($myPokemon->getUser()->getUsername());
            $myPokemon->gana();
        }else {
            $batalla->setGanador("Pokemon Aleatorio");
        }

        $entityManager->persist($myPokemon, $batalla);
        $entityManager->flush();

        return $this->render('pokedex/batalla.html.twig', [
            'randomPokemon' => $randomPokemon,
            'myPokemon' => $myPokemon,
            'fuerza' => $fuerza,
            'nivel' => $nivel,
            'ganador' => $batalla->getGanador()
        ]);
    }
    
    #[Route('/new', name: 'app_pokedex_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pokedex = new Pokedex();
        $form = $this->createForm(PokedexType::class, $pokedex);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pokedex);
            $entityManager->flush();

            return $this->redirectToRoute('app_pokedex_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pokedex2/new.html.twig', [
            'pokedex' => $pokedex,
            'form' => $form,
        ]);
    }
}
