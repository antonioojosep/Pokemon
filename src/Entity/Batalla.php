<?php

namespace App\Entity;

use App\Repository\BatallaRepository;
use App\Repository\PokedexRepository;
use App\Repository\PokemonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BatallaRepository::class)]
class Batalla
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pokedex $pokemon1 = null;

    #[ORM\ManyToOne]
    private ?Pokedex $pokemon2 = null;

    #[ORM\ManyToOne]
    private ?User $ganador = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user1 = null;

    #[ORM\ManyToOne]
    private ?User $user2 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPokemon1(): ?Pokedex
    {
        return $this->pokemon1;
    }

    public function setPokemon1(?Pokedex $pokemon1): static
    {
        $this->pokemon1 = $pokemon1;

        return $this;
    }

    public function getPokemon2(): ?Pokedex
    {
        return $this->pokemon2;
    }

    public function setPokemon2(?Pokedex $pokemon2): static
    {
        $this->pokemon2 = $pokemon2;

        return $this;
    }

    public function getGanador(): ?User
    {
        return $this->ganador;
    }

    public function setGanador(?User $ganador): static
    {
        $this->ganador = $ganador;

        return $this;
    }

    public function getUser1(): ?User
    {
        return $this->user1;
    }

    public function setUser1(?User $user1): static
    {
        $this->user1 = $user1;

        return $this;
    }

    public function getUser2(): ?User
    {
        return $this->user2;
    }

    public function setUser2(?User $user2): static
    {
        $this->user2 = $user2;

        return $this;
    }

    public function joinBattle(?Pokedex $pokemon2): ?Batalla
    {
        $this->setPokemon2($pokemon2);

        if (($this->getPokemon1()->getFuerza() * $this->getPokemon1()->getNivel()) >= ($pokemon2->getFuerza() * $pokemon2->getNivel())) {
            $this->setGanador($this->getUser1());
            $this->getPokemon1()->gana();
            $pokemon2->pierde();
        } else {
            $this->setGanador($this->getUser1());
            $pokemon2->gana();
            $this->getPokemon1()->pierde();
        }
        return $this;
    }
}
