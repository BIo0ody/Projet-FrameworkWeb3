<?php

namespace App\Entity;

use App\Repository\CarteBancaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarteBancaireRepository::class)]
class CarteBancaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $expiration = null;

    #[ORM\Column(length: 3)]
    private ?string $codeSecuriter = null;

    #[ORM\ManyToOne(inversedBy: 'carteBancaires')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getExpiration(): ?\DateTime
    {
        return $this->expiration;
    }

    public function setExpiration(\DateTime $expiration): static
    {
        $this->expiration = $expiration;

        return $this;
    }

    public function getCodeSecuriter(): ?string
    {
        return $this->codeSecuriter;
    }

    public function setCodeSecuriter(string $codeSecuriter): static
    {
        $this->codeSecuriter = $codeSecuriter;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
