<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjetRepository")
 */
class Projet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_livraison;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TempsProduction", mappedBy="projet")
     */
    private $tempsProductions;


    public function __construct()
    {
        $this->employe = new ArrayCollection();
        $this->tempsProductions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateLivraison(): ?\DateTimeInterface
    {
        return $this->date_livraison;
    }

    public function setDateLivraison(?\DateTimeInterface $date_livraison): self
    {
        $this->date_livraison = $date_livraison;

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
            $tempsProduction->setProjet($this);
        }

        return $this;
    }

    public function removeTempsProduction(TempsProduction $tempsProduction): self
    {
        if ($this->tempsProductions->contains($tempsProduction)) {
            $this->tempsProductions->removeElement($tempsProduction);
            // set the owning side to null (unless already changed)
            if ($tempsProduction->getProjet() === $this) {
                $tempsProduction->setProjet(null);
            }
        }

        return $this;
    }

}
