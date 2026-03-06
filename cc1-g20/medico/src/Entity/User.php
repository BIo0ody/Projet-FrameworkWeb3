<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 15)]
    private ?string $ssn = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(length: 100)]
    private ?string $genre = null;

    /**
     * @var Collection<int, Consultation>
     */
    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'patient')]
    private Collection $consultationsPatient;

    /**
     * @var Collection<int, Consultation>
     */
    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'medecin')]
    private Collection $consultationsMedecin;

    /**
     * @var Collection<int, CarteBancaire>
     */
    #[ORM\OneToMany(targetEntity: CarteBancaire::class, mappedBy: 'user')]
    private Collection $carteBancaires;


    public function __construct()
    {
        $this->consultationsPatient = new ArrayCollection();
        $this->consultationsMedecin = new ArrayCollection();
        $this->carteBancaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    public function getSsn(): ?string
    {
        return $this->ssn;
    }

    public function setSsn(string $ssn): static
    {
        $this->ssn = $ssn;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Collection<int, Consultation>
     */
    public function getConsultationsPatient(): Collection
    {
        return $this->consultationsPatient;
    }

    public function addConsultationsPatient(Consultation $consultationsPatient): static
    {
        if (!$this->consultationsPatient->contains($consultationsPatient)) {
            $this->consultationsPatient->add($consultationsPatient);
            $consultationsPatient->setPatient($this);
        }

        return $this;
    }

    public function removeConsultationsPatient(Consultation $consultationsPatient): static
    {
        if ($this->consultationsPatient->removeElement($consultationsPatient)) {
            // set the owning side to null (unless already changed)
            if ($consultationsPatient->getPatient() === $this) {
                $consultationsPatient->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Consultation>
     */
    public function getConsultationsMedecin(): Collection
    {
        return $this->consultationsMedecin;
    }

    public function addConsultationsMedecin(Consultation $consultationsMedecin): static
    {
        if (!$this->consultationsMedecin->contains($consultationsMedecin)) {
            $this->consultationsMedecin->add($consultationsMedecin);
            $consultationsMedecin->setMedecin($this);
        }

        return $this;
    }

    public function removeConsultationsMedecin(Consultation $consultationsMedecin): static
    {
        if ($this->consultationsMedecin->removeElement($consultationsMedecin)) {
            // set the owning side to null (unless already changed)
            if ($consultationsMedecin->getMedecin() === $this) {
                $consultationsMedecin->setMedecin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CarteBancaire>
     */
    public function getCarteBancaires(): Collection
    {
        return $this->carteBancaires;
    }

    public function addCarteBancaire(CarteBancaire $carteBancaire): static
    {
        if (!$this->carteBancaires->contains($carteBancaire)) {
            $this->carteBancaires->add($carteBancaire);
            $carteBancaire->setUser($this);
        }

        return $this;
    }

    public function removeCarteBancaire(CarteBancaire $carteBancaire): static
    {
        if ($this->carteBancaires->removeElement($carteBancaire)) {
            // set the owning side to null (unless already changed)
            if ($carteBancaire->getUser() === $this) {
                $carteBancaire->setUser(null);
            }
        }

        return $this;
    }

}
