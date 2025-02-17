<?php

namespace App\Entity;

use App\Repository\BatallaEquiposRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;

#[ORM\Entity(repositoryClass: BatallaEquiposRepository::class)]
class BatallaEquipos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Batalla $batalla1 = null;

    #[ORM\ManyToOne]
    private ?Batalla $batalla2 = null;

    #[ORM\ManyToOne]
    private ?Batalla $batalla3 = null;

    #[ORM\ManyToOne]
    private ?User $user1 = null;

    #[ORM\ManyToOne]
    private ?User $user2 = null;

    #[ORM\ManyToOne]
    private ?User $ganador = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBatalla1(): ?Batalla
    {
        return $this->batalla1;
    }

    public function setBatalla1(?Batalla $batalla1): static
    {
        $this->batalla1 = $batalla1;

        return $this;
    }

    public function getBatalla2(): ?Batalla
    {
        return $this->batalla2;
    }

    public function setBatalla2(?Batalla $batalla2): static
    {
        $this->batalla2 = $batalla2;

        return $this;
    }

    public function getBatalla3(): ?Batalla
    {
        return $this->batalla3;
    }

    public function setBatalla3(?Batalla $batalla3): static
    {
        $this->batalla3 = $batalla3;

        return $this;
    }

    public function setBatalla(?Batalla $batalla): static
    {
        if ($this->batalla1 == null) {
            $this->batalla1 = $batalla;
        }elseif ($this->batalla2 ==null) {
            $this->batalla2 = $batalla;
        }else {
            $this->batalla3 = $batalla;
        }
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

    public function getGanador(): ?User
    {
        return $this->ganador;
    }

    public function setGanador(?User $ganador): static
    {
        $this->ganador = $ganador;

        return $this;
    }

    public function getAllBattles(): array
    {
        return [$this->getBatalla1(), $this->getBatalla2(), $this->getBatalla3()];
    }

    public function getAllPokemons(): array
    {
        $pokemons = [];
        foreach ($this->getAllBattles() as $battle) {
            if ($battle->getPokemon1() != null) {
                $pokemons[] = $battle->getPokemon1();
            }
        }
        return $pokemons;
    }

    public function getAllPokemons2(): array
    {
        $pokemons = [];
        foreach ($this->getAllBattles() as $battle) {
            if ($battle->getPokemon2() != null) {
                $pokemons[] = $battle->getPokemon2();
            }
        }
        return $pokemons;
    }

    public function addPokemons2(Pokedex $pokemon)
    {
        if ($this->batalla1->getPokemon2() == null) {
            $this->batalla1->joinBattle($pokemon);
            return true;
        }elseif ($this->batalla2->getPokemon2() == null) {
            $this->batalla2->joinBattle($pokemon);
            return true;
        } elseif ($this->batalla3->getPokemon2() == null) {
            $this->batalla3->joinBattle($pokemon);
            return false;
        } else {
            return false;
        }
    }

    public function addArrayPokemon2(array $pokemons) : void
    {
         foreach ($pokemons as $pokemon) {
             $this->addPokemons2($pokemon);
         }
    }

    public function addPokemons1(Pokedex $pokemon)
    {
        if ($this->batalla1->getPokemon1() == null) {
            $this->batalla1->setPokemon1($pokemon);
            return true;
        }elseif ($this->batalla2->getPokemon1() == null) {
            $this->batalla2->setPokemon1($pokemon);
            return true;
        } elseif ($this->batalla3->getPokemon1() == null) {
            $this->batalla3->setPokemon1($pokemon);
            return false;
        } else {
            return false;
        }
    }

    public function setAllUser1(User $user) : void
    {
        $this->user1 = $user;
        $battles = $this->getAllBattles();
        foreach ($battles as $battle) {
            $battle->setUser1($user);
        }
    }

    public function setAllUser2(User $user) : void
    {
        $this->user2 = $user;
        $battles = $this->getAllBattles();
        foreach ($battles as $battle) {
            $battle->setUser2($user);
        }
    }

    public function luchar()
    {
        $batallas = $this->getAllBattles();
        $user1 = 0;
        $user2 = 0;
        foreach ($batallas as $battle) {
            if ($battle->getGanador() === $this->user1) {
                $user1++;
            } elseif ($battle->getGanador() === $this->user2) {
                $user2++;
            }
        }
        if ($user1 >= 2) {
            $this->setGanador($this->user1);
        }elseif ($user2 >= 2) {
            $this->setGanador($this->user2);
        }
    }

    // Comprueba si el pokemon ya a sido añadido al equipo 1
    public function isAddedToTeam1(Pokedex $pokemon) : bool
    {
        return $this->batalla1->getPokemon1() === $pokemon ||
               $this->batalla2->getPokemon1() === $pokemon ||
               $this->batalla3->getPokemon1() === $pokemon;
    }
    // Comprueba si el pokemon ya a sido añadido al equipo 2
    public function isAddedToTeam2(Pokedex $pokemon) : bool
    {
        return $this->batalla1->getPokemon2() === $pokemon ||
               $this->batalla2->getPokemon2() === $pokemon ||
               $this->batalla3->getPokemon2() === $pokemon;
    }
}
