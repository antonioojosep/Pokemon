<?php

namespace App\Controller;

use App\Entity\Batalla;
use App\Entity\BatallaEquipos;
use App\Repository\BatallaEquiposRepository;
use App\Repository\PokedexRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BatallaEquiposController extends AbstractController
{
    #[Route('/batalla/equipos', name: 'app_batalla_equipos')]
    public function index(BatallaEquiposRepository $batallaEquiposRepository): Response
    {
        return $this->render('batalla_equipos/index.html.twig', [
            'batallas' => $batallaEquiposRepository->findBy(['user2' => $this->getUser(), 'ganador' => null]),
        ]);
    }

    #[Route('/new', name: 'app_batallaEquipos_new', methods:['GET','POST'])]
    public function new(EntityManagerInterface $entityManager, PokedexRepository $pokedexRepository): Response
    {
        $user = $this->getUser();
        $batalla1 = new Batalla();
        $batalla2 = new Batalla();
        $batalla3 = new Batalla();

        $batallaEquipos = new BatallaEquipos();
        $batallaEquipos->setBatalla1($batalla1);
        $batallaEquipos->setBatalla2($batalla2);
        $batallaEquipos->setBatalla3($batalla3);

        $entityManager->persist($batalla1);
        $entityManager->persist($batalla2);
        $entityManager->persist($batalla3);
        $entityManager->persist($batallaEquipos);
        $entityManager->flush();
        $batallaEquipos->setAllUser1($user);
        $batallaEquipos->setUser1($user);
        $entityManager->persist($batallaEquipos);
        $entityManager->flush();


        return $this->redirectToRoute('app_batallaEquipos_choosePokemon', ['batalla' => $batallaEquipos->getId()]);
    }

    #[Route('/choosePokemon/{batalla}' , name: 'app_batallaEquipos_choosePokemon', methods:['POST','GET'])]
    public function choosePokemon(Request $request, BatallaEquipos $batalla, EntityManagerInterface $entityManager, PokedexRepository $pokedexRepository, UserRepository $userRepository): Response
    {
        $allPokemons = $pokedexRepository->findByUserNoDerrotado($this->getUser());

        if ($request->get('pokemon')) {
            $pokemon = $pokedexRepository->find($request->get('pokemon'));
            $response = $batalla->addPokemons1($pokemon, $this->getUser(), $entityManager);
            $entityManager->persist($batalla);
            $entityManager->flush();
            if ($response == true) {
                $pokemonsInBattle = $batalla->getAllPokemons();

                $pokemons = array_udiff($allPokemons, $pokemonsInBattle, function ($a, $b) {
                    return $a->getId() - $b->getId(); 
                });

                return $this->render('batalla_equipos/choose_pokemon.html.twig', [
                    'batalla' => $batalla,
                    'pokemons' => $pokemons,
                ]);
            }else {
                return $this->render('batalla_equipos/choose_oponent.html.twig', [
                    'batalla' => $batalla,
                    'users' => $userRepository->findAllDiferent($this->getUser())
                ]);
            }
        }else {
            return $this->render('batalla_equipos/choose_pokemon.html.twig', [
                'batalla' => $batalla,
                'pokemons' => $allPokemons,
            ]);
        }
    }

    #[Route('/addPokemon/{batalla}' , name: 'app_batallaEquipos_addPokemon', methods:['POST','GET'])]
    public function addPokemon(Request $request, BatallaEquipos $batalla, EntityManagerInterface $entityManager, PokedexRepository $pokedexRepository, UserRepository $userRepository): Response
    {
        $allPokemons = $pokedexRepository->findByUserNoDerrotado($this->getUser());

        if ($request->get('pokemon')) {
            $pokemon = $pokedexRepository->find($request->get('pokemon'));
            $response = $batalla->addPokemons2($pokemon, $this->getUser(), $entityManager);
            $entityManager->persist($batalla);
            $entityManager->flush();
            if ($response == true) {
                $pokemonsInBattle = $batalla->getAllPokemons2();

                $pokemons = array_udiff($allPokemons, $pokemonsInBattle, function ($a, $b) {
                    return $a->getId() - $b->getId(); 
                });

                return $this->render('batalla_equipos/addPokemon.html.twig', [
                    'pokemonsOponente' => $batalla->getAllPokemons(),
                    'batalla' => $batalla,
                    'pokemons' => $pokemons,
                ]);
            }else {
                $batalla->luchar();
                $entityManager->persist($batalla);
                $entityManager->flush();
                return $this->render('batalla_equipos/batalla.html.twig', [
                    'ganador' => $batalla->getGanador(),
                ]);
            }
        }else {
            return $this->render('batalla_equipos/addPokemon.html.twig', [
                'pokemonsOponente' => $batalla->getAllPokemons(),
                'batalla' => $batalla,
                'pokemons' => $allPokemons,
            ]);
        }
    }

    #[Route('/oponents/{batalla}' , name: 'app_batallaEquipos_oponent' , methods: ['POST'])]
    public function oponent(Request $request, BatallaEquipos $batalla, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $userId = $request->get('user');
        $user = $userRepository->findOneBy(['id' => $userId]);
        $batalla->setAllUser2($user);
        $entityManager->persist($batalla);
        $entityManager->flush();

        return $this->redirectToRoute('app_pokedex_index');
    }

    #[Route('/rechazar/{batalla}' , name: 'app_batallaEquipos_rechazar', methods:['GET'])]
    public function rechazar(BatallaEquipos $batalla, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($batalla);
        $entityManager->flush();

        return $this->redirectToRoute('app_batalla_index');
    }
}
