<?php

namespace App\Entity;

use App\Repository\AdresseRepository;
use App\Entity\Utilisateur;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdresseRepository::class)]
class Adresse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La rue est obligatoire')]
    private ?string $rue = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'La ville est obligatoire')]
    private ?string $ville = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Le code postal est obligatoire')]
    #[Assert\Regex(
        pattern: '/^[0-9]{5}$/',
        message: 'Le code postal doit contenir 5 chiffres'
    )]
    private ?string $codePostal = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le pays est obligatoire')]
    private ?string $pays = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    private ?string $prenom = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'adresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    public function getId(): ?int { return $this->id; }

    public function getRue(): ?string { return $this->rue; }
    public function setRue(string $rue): static { $this->rue = $rue; return $this; }

    public function getVille(): ?string { return $this->ville; }
    public function setVille(string $ville): static { $this->ville = $ville; return $this; }

    public function getCodePostal(): ?string { return $this->codePostal; }
    public function setCodePostal(string $codePostal): static { $this->codePostal = $codePostal; return $this; }

    public function getPays(): ?string { return $this->pays; }
    public function setPays(string $pays): static { $this->pays = $pays; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): static { $this->prenom = $prenom; return $this; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }
}
