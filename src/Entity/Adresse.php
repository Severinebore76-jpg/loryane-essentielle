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

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: 'Le numéro est obligatoire')]
    private ?string $numero = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le type de voie est obligatoire')]
    private ?string $typeVoie = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de la voie est obligatoire')]
    private ?string $nomVoie = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'La ville est obligatoire')]
    private ?string $ville = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Le code postal est obligatoire')]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9 -]+$/',
        message: 'Format de code postal invalide'
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

    #[ORM\Column(length: 20)]
    private ?string $type = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'adresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    // -------------------
    // GETTERS / SETTERS
    // -------------------

    public function getId(): ?int { return $this->id; }

    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(string $numero): static { $this->numero = $numero; return $this; }

    public function getTypeVoie(): ?string { return $this->typeVoie; }
    public function setTypeVoie(string $typeVoie): static { $this->typeVoie = $typeVoie; return $this; }

    public function getNomVoie(): ?string { return $this->nomVoie; }
    public function setNomVoie(string $nomVoie): static { $this->nomVoie = $nomVoie; return $this; }

    public function getVille(): ?string { return $this->ville; }
    public function setVille(string $ville): static { $this->ville = $ville; return $this; }

    public function getCodePostal(): ?string { return $this->codePostal; }
    public function setCodePostal(string $codePostal): static { $this->codePostal = $codePostal; return $this; }

    public function getPays(): ?string { return $this->pays; }
    public function setPays(string $pays): static { $this->pays = $pays; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = mb_strtoupper(trim($nom), 'UTF-8'); return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): static { $this->prenom = mb_convert_case(trim($prenom), MB_CASE_TITLE, 'UTF-8'); return $this; }

    public function getType(): ?string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getAdresseComplete(): string
    {
        return $this->numero . ' ' . $this->typeVoie . ' ' . $this->nomVoie;
    }
}
