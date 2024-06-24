<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
// #[ApiResource(
//     normalizationContext: ['groups' => ['ingredient:sql:read']],
//     denormalizationContext: ['groups' => ['ingredient:sql:write']]
// )]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ingredient:sql:read', 'ingredient:sql:write', 'recette:sql:read', 'recette:sql:write'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['ingredient:sql:read', 'ingredient:sql:write', 'recette:sql:read', 'recette:sql:write'])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'ingredients')]
    #[Groups(['ingredient:sql:read', 'ingredient:sql:write'])]
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
