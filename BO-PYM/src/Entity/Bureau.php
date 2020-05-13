<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BureauRepository")
 */
class Bureau implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Numero;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Etage;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprise", inversedBy="bureaux")
     */
    private $entreprise;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Batiment", inversedBy="Bureaux")
     */
    private $Batiment;

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'idBatiment' => $this->getBatiment()->getId(),
            'idEntreprise' => $this->getEntreprise()->getId(),
            'entreprise' => $this->getEntreprise()->getNom(),
            'urlEntreprise' => $this->getEntreprise()->getLogo(),
            'etage' => $this->getEtage(),
            'numero' => $this->getNumero(),
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBatiment(): ?Batiment
    {
        return $this->Batiment;
    }

    public function setBatiment(?Batiment $Batiment): self
    {
        $this->Batiment = $Batiment;

        return $this;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getEtage(): ?int
    {
        return $this->Etage;
    }

    public function setEtage(int $Etage): self
    {
        $this->Etage = $Etage;

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->Numero;
    }

    public function setNumero(int $Numero): self
    {
        $this->Numero = $Numero;

        return $this;
    }
}
