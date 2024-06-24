<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[MongoDB\Document]
// #[ApiResource(
//     normalizationContext: ['groups' => ['ingredient:nosql:read']],
//     denormalizationContext: ['groups' => ['ingredient:nosql:write']]
// )]
class Ingredient
{
    #[MongoDB\Id]
    #[Groups(['ingredient:nosql:read'])]
    private $id;

    #[MongoDB\Field(type: 'string')]
    #[Groups(['ingredient:nosql:read', 'ingredient:nosql:write', 'recette:nosql:read', 'recette:nosql:write'])]
    private $content;

    #[MongoDB\ReferenceOne(targetDocument: Recette::class, inversedBy: 'ingredients')]
    #[Groups(['ingredient:nosql:read', 'ingredient:nosql:write'])]
    private $recette;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getRecette(): ?Recette
    {
        return $this->recette;
    }

    public function setRecette(?Recette $recette): self
    {
        $this->recette = $recette;
        return $this;
    }
}


