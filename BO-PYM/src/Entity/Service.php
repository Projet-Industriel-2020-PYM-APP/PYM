<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServiceRepository")
 */
class Service implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;
    /**
     * @ManyToOne(targetEntity="ServiceCategorie")
     * @JoinColumn(name="$categorie_id", referencedColumnName="id")
     */
    private $categorie;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subtitle;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgUrl;
    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $actions = [];
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telephone;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website;

    public function setActions(array $actions): self
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $imgUrl = null;
        if ($this->getImgUrl()) {
            $imgUrl = "https://map-pym.com/sharedfolder/services/" . $this->getImgUrl();
        }

        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'categorie_id' => $this->getCategorie()->getId(),
            'subtitle' => $this->getSubtitle(),
            'address' => $this->getAddress(),
            'img_url' => $imgUrl,
            'actions' => $this->getActions(),
            'telephone' => $this->getTelephone(),
            'website' => $this->getWebsite(),
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCategorie(): ?ServiceCategorie
    {
        return $this->categorie;
    }

    public function setCategorie(ServiceCategorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getImgUrl()
    {
        return $this->imgUrl;
    }

    public function setImgUrl($imgUrl): self
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    public function getActions(): ?array
    {
        return $this->actions;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }
}
