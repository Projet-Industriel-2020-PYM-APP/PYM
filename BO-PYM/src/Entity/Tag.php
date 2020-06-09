<?php

namespace App\Entity;

use JsonSerializable;

class Tag implements JsonSerializable
{
    private $name;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->getName();
    }
}