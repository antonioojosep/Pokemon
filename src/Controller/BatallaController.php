<?php

namespace App\Controller;

use App\Entity\Batalla;
use App\Entity\Pokedex;
use App\Entity\User;
use App\Repository\BatallaRepository;
use App\Repository\PokedexRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/batalla')]
final class BatallaController extends AbstractController
{
    #[Route('/addUser/{batalla}', name: 'app_batalla_addUser', methods: ['GET','POST'])]
    public function addUser2(Request $request, Batalla $batalla,  UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $user2 = $request->request->get('user');
        $user = $userRepository->find($user2);
        $batalla->setUser2($user);

        $entityManager->persist($batalla);
        $entityManager->flush();

        return $this->redirectToRoute('app_pokedex_index');
    }
    #[Route('/', name: 'app_batalla_index', methods: ['GET'])]
    public function list(BatallaRepository $batallaRepository)
    {   
        return $this->render('batalla/index.html.twig', [
            'batallas' => $batallaRepository->findBy(['user2' => $this->getUser()]),
        ]);
    }

    #[Route('/unirse-batalla/{batallaId}/{pokemonId}', name: 'app_batalla_unirse', methods: ['GET'])]
    public function unirseBatalla(int $batallaId, int $pokemonId, BatallaRepository $batallaRepository, PokedexRepository $pokedexRepository, EntityManagerInterface $entityManager): Response
    {
        $pokemon2 = $pokedexRepository->find($pokemonId);
        $batalla = $batallaRepository->find($batallaId);

        $batalla = $batallaRepository->joinBattle($pokemon2, $batalla);

        $entityManager->persist($batalla);
        $entityManager->flush();

        return $this->render('batalla/show.html.twig', [
            'pokemon1' => $batalla->getPokemon2(),
            'pokemon2' => $batalla->getPokemon1(),
            'ganador' => $batalla->getGanador()
        ]);
    }

    #[Route('/new/{pokemon}', name: 'app_batalla_new', methods: ['GET'])]
    public function crearBatalla(Pokedex $pokemon, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user1 = $this->getUser();
        $users = $userRepository->findAllDiferent($user1);
        $batalla = new Batalla();
        $batalla->setPokemon1($pokemon);
        $batalla->setUser1($user1);

        $entityManager->persist($batalla);
        $entityManager->flush();

        return $this->render('batalla/new.html.twig', [
            'batalla' => $batalla,
            'pokemon' => $pokemon,
            'users' => $users,
        ]);
    }

    #[Route('/aceptar/{batalla}', name: 'app_batalla_aceptar', methods: ['GET'])]
    public function aceptar(Batalla $batalla, PokedexRepository $pokedexRepository, Request $request, EntityManagerInterface $entityManager)
    {
        if ($request->request->get('pokemon') != null) {
            $entityManager->persist($batalla);
            $entityManager->flush();
        }else {
            return $this->render('pokedex/index.html.twig', [
                'modo' => 'batalla',
                'batalla' => $batalla,
                'pokemons' => $pokedexRepository->findByUser($this->getUser())
            ]);
        }
    }
}
