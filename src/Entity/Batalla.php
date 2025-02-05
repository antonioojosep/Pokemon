<?php

namespace App\Entity;

use App\Repository\BatallaRepository;
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
    private ?Pokedex $pokemon_usuario = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pokemon $pokemon_aleatorio = null;

    #[ORM\Column(length: 255)]
    private ?string $ganador = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPokemonUsuario(): ?Pokedex
    {
        return $this->pokemon_usuario;
    }

    public function setPokemonUsuario(?Pokedex $pokemon_usuario): static
    {
        $this->pokemon_usuario = $pokemon_usuario;

        return $this;
    }

    public function getPokemonAleatorio(): ?Pokemon
    {
        return $this->pokemon_aleatorio;
    }

    public function setPokemonAleatorio(?Pokemon $pokemon_aleatorio): static
    {
        $this->pokemon_aleatorio = $pokemon_aleatorio;

        return $this;
    }

    public function getGanador(): ?string
    {
        return $this->ganador;
    }

    public function setGanador(string $ganador): static
    {
        $this->ganador = $ganador;

        return $this;
    }
}
