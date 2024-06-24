<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Odm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;

#[MongoDB\Document]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/nosql/recettes/{id}',
            normalizationContext: ['groups' => ['recette:nosql:read']]
        ),
        new GetCollection(
            uriTemplate: '/nosql/recettes',
            normalizationContext: ['groups' => ['recette:nosql:read']]
        )
        // ,
        // new Post(
        //     uriTemplate: '/nosql/recettes',
        //     denormalizationContext: ['groups' => ['recette:nosql:write']]
        // )
    ],
    paginationEnabled: false,
    normalizationContext: ['groups' => ['recette:nosql:read']],
    denormalizationContext: ['groups' => ['recette:nosql:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'ingredients.content' => 'partial'
])]
class Recette
{
    #[MongoDB\Id]
    #[Groups(['recette:nosql:read'])]
    private $id;

    #[MongoDB\Field(type: 'string')]
    #[Groups(['recette:nosql:read', 'recette:nosql:write'])]
    private $title;

    #[MongoDB\ReferenceMany(targetDocument: Ingredient::class, mappedBy: 'recette', cascade: ["persist", "remove"])]
    #[Groups(['recette:nosql:read', 'recette:nosql:write'])]
    private $ingredients;

    #[MongoDB\ReferenceMany(targetDocument: Etape::class, mappedBy: 'recette', cascade: ["persist", "remove"])]
    #[Groups(['recette:nosql:read', 'recette:nosql:write'])]
    private $etapes;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->etapes = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
            $ingredient->setRecette($this);
        }
        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): self
    {
        if ($this->ingredients->removeElement($ingredient)) {
            if ($ingredient->getRecette() === $this) {
                $ingredient->setRecette(null);
            }
        }
        return $this;
    }

    public function getEtapes(): Collection
    {
        return $this->etapes;
    }

    public function addEtape(Etape $etape): self
    {
        if (!$this->etapes->contains($etape)) {
            $this->etapes->add($etape);
            $etape->setRecette($this);
        }
        return $this;
    }

    public function removeEtape(Etape $etape): self
    {
        if ($this->etapes->removeElement($etape)) {
            if ($etape->getRecette() === $this) {
                $etape->setRecette(null);
            }
        }
        return $this;
    }
}

