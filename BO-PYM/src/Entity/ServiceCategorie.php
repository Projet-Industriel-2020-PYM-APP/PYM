<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServiceCategorieRepository")
 */
class ServiceCategorie implements JsonSerializable
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $primaryColor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgUrl;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $action;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrimaryColor(): ?string
    {
        return $this->primaryColor;
    }

    public function setPrimaryColor(?string $primaryColor): self
    {
        $this->primaryColor = $primaryColor;

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

    public function getAction(): ?Action
    {
        return $this->action;
    }

    public function setAction(?Action $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $imgUrl = null;
        if ($this->getImgUrl()) {
            $imgUrl = "https://map-pym.com/sharedfolder/service_categories/" . $this->getImgUrl();
        }

        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'primary_color' => $this->getPrimaryColor(),
            'img_url' => $imgUrl,
            'action' => $this->getAction(),
        ];
    }
}
