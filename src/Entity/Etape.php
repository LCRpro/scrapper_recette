<?php

namespace App\Entity;

use App\Repository\EtapeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: EtapeRepository::class)]
// #[ApiResource(
//     normalizationContext: ['groups' => ['etape:sql:read']],
//     denormalizationContext: ['groups' => ['etape:sql:write']]
// )]
class Etape
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['etape:sql:read', 'etape:sql:write', 'recette:sql:read', 'recette:sql:write'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['etape:sql:read', 'etape:sql:write', 'recette:sql:read', 'recette:sql:write'])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'etapes')]
    #[Groups(['etape:sql:read', 'etape:sql:write'])]
    private ?Recette $recette = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['etape:sql:read', 'etape:sql:write', 'recette:sql:read', 'recette:sql:write'])]
    private ?int $step = null;

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

    public function getStep(): ?int
    {
        return $this->step;
    }

    public function setStep(?int $step): static
    {
        $this->step = $step;

        return $this;
    }
}


