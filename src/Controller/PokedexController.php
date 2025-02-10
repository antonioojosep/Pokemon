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

    #[Route('/entrenar/{id}', name: 'app_pokedex_entrenar', methods: ['GET', 'POST'])]
    public function entrenar(int $id, PokedexRepository $pokedexRepository, EntityManagerInterface $entityManager) : Response
    {
        $pokemon = $pokedexRepository->findOneBy(['id' => $id]);
        
        if ($pokemon->isDerrotado()) {
            $this->addFlash('error', 'No puedes entrenar a un Pokémon derrotado.');
            return $this->redirectToRoute('app_pokedex_index');
        }
        
        $pokemon->entrenar();
        $entityManager->persist($pokemon);
        $entityManager->flush();

        return $this->redirectToRoute('app_pokedex_index');
    }

    #[Route('/luchar/{id}', name: 'app_pokedex_luchar', methods: ['GET', 'POST'])]
    public function luchar(int $id, PokemonRepository $pokemonRepository, PokedexRepository $pokedexRepository, EntityManagerInterface $entityManager)
    {
        $myPokemon = $pokedexRepository->findOneBy(['id' => $id]);
        
        if ($myPokemon->isDerrotado()) {
            $this->addFlash('error', 'No puedes luchar con un Pokémon derrotado.');
            return $this->redirectToRoute('app_pokedex_index');
        }

        $randomPokemon = $pokemonRepository->getRandomPokemon();
        $fuerza = rand(10, $myPokemon->getFuerza() + 10);
        $nivel = rand(1, $myPokemon->getNivel() + 2);
        $batalla = new Batalla();
        $batalla->setPokemonAleatorio($randomPokemon);
        $batalla->setPokemonUsuario($myPokemon);

        if (($myPokemon->getFuerza() * $myPokemon->getNivel()) >= ($fuerza * $nivel)) {
            $batalla->setGanador($myPokemon->getUser()->getUsername());
            $myPokemon->gana();
        } else {
            $batalla->setGanador("Pokemon Aleatorio");
            $myPokemon->pierde();
        }

        $entityManager->persist($myPokemon);
        $entityManager->persist($batalla);
        $entityManager->flush();

        return $this->render('pokedex/batalla.html.twig', [
            'randomPokemon' => $randomPokemon,
            'myPokemon' => $myPokemon,
            'fuerza' => $fuerza,
            'nivel' => $nivel,
            'ganador' => $batalla->getGanador()
        ]);
    }

    #[Route('/evolucionar/{id}', name: 'app_pokedex_evolucionar', methods: ['GET'])]
    public function evolucionar(int $id, PokedexRepository $pokedexRepository, EntityManagerInterface $entityManager): Response
    {
        $pokemon = $pokedexRepository->findOneBy(['id' => $id]);
        
        // Verificar si el Pokémon está derrotado
        if ($pokemon->isDerrotado()) {
            $this->addFlash('error', 'No puedes evolucionar a un Pokémon derrotado.');
            return $this->redirectToRoute('app_pokedex_index');
        }
        
        // Verificar si puede evolucionar
        if (!$pokemon->getPokemon()->getEvolucion() || 
            !$pokemon->getPokemon()->getNivelEvolucion() || 
            $pokemon->getNivel() < $pokemon->getPokemon()->getNivelEvolucion()) {
            $this->addFlash('error', 'Este Pokémon no puede evolucionar todavía.');
            return $this->redirectToRoute('app_pokedex_index');
        }

        $pokemon->setPokemon($pokemon->getPokemon()->getEvolucion());
        
        $entityManager->persist($pokemon);
        $entityManager->flush();

        $this->addFlash('success', '¡Tu Pokémon ha evolucionado con éxito!');
        return $this->redirectToRoute('app_pokedex_index');
    }

    #[Route('/resucitar', name: 'app_pokedex_resucitar', methods: ['GET'])]
    public function resucitar(PokedexRepository $pokedexRepository, EntityManagerInterface $entityManager): Response
    {
        $pokemonsDerrotados = $pokedexRepository->findBy([
            'user' => $this->getUser(),
            'derrotado' => true
        ]);

        return $this->render('pokedex/resucitar.html.twig', [
            'pokemonsDerrotados' => $pokemonsDerrotados
        ]);
    }

    #[Route('/resucitar/{id}', name: 'app_pokedex_resucitar_pokemon', methods: ['GET'])]
    public function resucitarPokemon(int $id, PokedexRepository $pokedexRepository, EntityManagerInterface $entityManager): Response
    {
        $pokemon = $pokedexRepository->findOneBy(['id' => $id]);
        
        if (!$pokemon->isDerrotado()) {
            $this->addFlash('error', 'Este Pokémon no necesita ser resucitado.');
            return $this->redirectToRoute('app_pokedex_index');
        }

        $pokemon->gana(); // Esto cambia derrotado a false
        $entityManager->persist($pokemon);
        $entityManager->flush();

        $this->addFlash('success', '¡Tu Pokémon ha sido resucitado con éxito!');
        return $this->redirectToRoute('app_pokedex_index');
    }

    #[Route('/subir-nivel/{id}', name: 'app_pokedex_subir_nivel', methods: ['GET'])]
    public function subirNivel(int $id, PokedexRepository $pokedexRepository, EntityManagerInterface $entityManager): Response
    {
        $pokemon = $pokedexRepository->findOneBy(['id' => $id]);
        
        if (!$pokemon) {
            throw $this->createNotFoundException('No se encontró el Pokémon');
        }

        $pokemon->setNivel($pokemon->getNivel() + 1);
        $pokemon->setFuerza($pokemon->getFuerza() + 5);
        
        $entityManager->persist($pokemon);
        $entityManager->flush();

        $this->addFlash('success', '¡Tu Pokémon ha subido de nivel!');
        return $this->redirectToRoute('app_pokedex_index');
    }

    #[Route('/capturar/{id}', name: 'app_pokedex_capturar', methods: ['GET'])]
    public function capturar(int $id, PokemonRepository $pokemonRepository, EntityManagerInterface $entityManager): Response
    {
        $pokemon = $pokemonRepository->find($id);
        $success = (bool)random_int(0, 1); // 50% de probabilidad de captura
        
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
