<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokemonRepository::class)]
class Pokemon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $imagen = null;

    #[ORM\Column(length: 30)]
    private ?string $tipo = null;

    #[ORM\Column(name: "nivel_evolucion", nullable: true)]
    private ?int $nivelEvolucion = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(name: "evolucion_id", nullable: true)]
    private ?self $evolucion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(string $imagen): static
    {
        $this->imagen = $imagen;
        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): static
    {
        $this->tipo = $tipo;
        return $this;
    }

    public function getNivelEvolucion(): ?int
    {
        return $this->nivelEvolucion;
    }

    public function setNivelEvolucion(?int $nivelEvolucion): static
    {
        $this->nivelEvolucion = $nivelEvolucion;
        return $this;
    }

    public function getEvolucion(): ?self
    {
        return $this->evolucion;
    }

    public function setEvolucion(?self $evolucion): static
    {
        $this->evolucion = $evolucion;
        return $this;
    }
}
