<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TempsProductionRepository")
 */
class TempsProduction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Projet", inversedBy="tempsProductions")
     */
    private $projet;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Employe", inversedBy="tempsProductions")
     */
    private $employe;

    /**
     * @ORM\Column(type="integer")
     */
    private $heuresTravailles;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateSaisie;

    public function __construct()
    {
        $this->dateSaisie = new \DateTime();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(?Projet $projet): self
    {
        $this->projet = $projet;

        return $this;
    }

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): self
    {
        $this->employe = $employe;

        return $this;
    }

    public function getHeuresTravailles(): ?int
    {
        return $this->heuresTravailles;
    }

    public function setHeuresTravailles(int $heuresTravailles): self
    {
        $this->heuresTravailles = $heuresTravailles;

        return $this;
    }

    public function getDateSaisie(): ?\DateTimeInterface
    {
        return $this->dateSaisie;
    }

    public function setDateSaisie(\DateTimeInterface $dateSaisie): self
    {
        $this->dateSaisie = $dateSaisie;

        return $this;
    }
}
