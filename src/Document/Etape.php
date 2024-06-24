<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[MongoDB\Document]
// #[ApiResource(
//     normalizationContext: ['groups' => ['etape:nosql:read']],
//     denormalizationContext: ['groups' => ['etape:nosql:write']]
// )]
class Etape
{
    #[MongoDB\Id]
    #[Groups(['etape:nosql:read'])]
    private $id;

    #[MongoDB\Field(type: 'string')]
    #[Groups(['etape:nosql:read', 'etape:nosql:write', 'recette:nosql:read', 'recette:nosql:write'])]
    private $content;

    #[MongoDB\ReferenceOne(targetDocument: Recette::class, inversedBy: 'etapes')]
    #[Groups(['etape:nosql:read', 'etape:nosql:write'])]
    private $recette;

    #[MongoDB\Field(type: 'int')]
    #[Groups(['etape:nosql:read', 'etape:nosql:write', 'recette:nosql:read', 'recette:nosql:write'])]
    private $step;

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

    public function getStep(): ?int
    {
        return $this->step;
    }

    public function setStep(int $step): self
    {
        $this->step = $step;
        return $this;
    }
}




