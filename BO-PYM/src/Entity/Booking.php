<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

/**
 * @CustomAssert\BookingSuperpose
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */
class Booking implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Valid
     * @ManyToOne(targetEntity="Service")
     * @JoinColumn(name="$service_id", referencedColumnName="id")
     */
    private $service;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Expression(
     *     "this.getStartDate() < this.getEndDate()",
     *     message="La date de début doit être avant la date de fin.")
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="boolean", options={"default" : true})
     */
    private $superpose;

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'service_id' => $this->getService() ? $this->getService()->getId() : null,
            'title' => $this->getTitle(),
            'start_date' => $this->getStartDate()->format(DateTime::ISO8601),
            'end_date' => $this->getEndDate()->format(DateTime::ISO8601),
            'superpose' => $this->getSuperpose(),
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(DateTime $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getSuperpose(): ?bool
    {
        return $this->superpose;
    }

    /**
     * @param bool $superpose
     * @return $this
     */
    public function setSuperpose(bool $superpose): self
    {
        $this->superpose = $superpose;
        return $this;
    }
}
