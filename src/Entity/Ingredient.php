<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['recette:read', 'recette:write'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['recette:read', 'recette:write'])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'ingredients')]
    #[Groups(['recette:read', 'recette:write'])]
    private ?Recette $recette = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getRecette(): ?Recette
    {
        return $this->recette;
    }

    public function setRecette(?Recette $recette): static
    {
        $this->recette = $recette;

        return $this;
    }
}
