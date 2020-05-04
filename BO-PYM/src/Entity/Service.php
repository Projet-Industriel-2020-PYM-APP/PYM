<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\Column(type="integer")
     */
    private $categorie_id;
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
    private $img_url;
    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $actions;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telephone;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website;

    public function __construct()
    {
        $this->actions = new ArrayCollection();
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

    public function getCategorieId(): ?int
    {
        return $this->categorie_id;
    }

    public function setCategorieId(int $categorie_id): self
    {
        $this->categorie_id = $categorie_id;

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

    public function getImgUrl(): ?string
    {
        return $this->img_url;
    }

    public function setImgUrl(?string $img_url): self
    {
        $this->img_url = $img_url;

        return $this;
    }

    public function getActions(): ?Collection
    {
        return $this->actions;
    }

    public function setActions(?array $actions): self
    {
        $this->actions = $actions;

        return $this;
    }

    public function addActions(Action $action): self
    {
        if (!$this->actions->contains($action)) {
            $this->actions[] = $action;
        }

        return $this;
    }

    public function removeActions(Action $action): self
    {
        if ($this->actions->contains($action)) {
            $this->actions->removeElement($action);
        }

        return $this;
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

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'categorie_id' => $this->categorie_id,
            'subtitle' => $this->subtitle,
            'address' => $this->address,
            'img_url' => $this->img_url,
            'actions' => $this->actions,
            'telephone' => $this->telephone,
            'website' => $this->website,
        ];
    }
}
