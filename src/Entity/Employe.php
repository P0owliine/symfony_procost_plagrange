<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmployeRepository")
 */
class Employe implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="float")
     */
    private $coutHoraire;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEmbauche;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Metier")
     * @ORM\JoinColumn(nullable=false)
     */
    private $metier;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TempsProduction", mappedBy="employe")
     */
    private $tempsProductions;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img;

    public function __construct()
    {
        $this->tempsProductions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }


    public function getCoutHoraire(): ?float
    {
        return $this->coutHoraire;
    }

    public function setCoutHoraire(float $coutHoraire): self
    {
        $this->coutHoraire = $coutHoraire;

        return $this;
    }

    public function getDateEmbauche(): ?\DateTimeInterface
    {
        return $this->dateEmbauche;
    }

    public function setDateEmbauche(\DateTimeInterface $dateEmbauche): self
    {
        $this->dateEmbauche = $dateEmbauche;

        return $this;
    }

    public function getMetier(): ?Metier
    {
        return $this->metier;
    }

    public function setMetier(?Metier $metier): self
    {
        $this->metier = $metier;

        return $this;
    }

    /**
     * @return Collection|TempsProduction[]
     */
    public function getTempsProductions(): Collection
    {
        return $this->tempsProductions;
    }

    public function addTempsProduction(TempsProduction $tempsProduction): self
    {
        if (!$this->tempsProductions->contains($tempsProduction)) {
            $this->tempsProductions[] = $tempsProduction;
            $tempsProduction->setEmploye($this);
        }

        return $this;
    }

    public function removeTempsProduction(TempsProduction $tempsProduction): self
    {
        if ($this->tempsProductions->contains($tempsProduction)) {
            $this->tempsProductions->removeElement($tempsProduction);
            // set the owning side to null (unless already changed)
            if ($tempsProduction->getEmploye() === $this) {
                $tempsProduction->setEmploye(null);
            }
        }

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

        return $this;
    }

}
