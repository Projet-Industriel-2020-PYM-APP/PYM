<?php

namespace App\Entity;

use JsonSerializable;

class Action implements JsonSerializable
{
    private $htmlUrl;
    private $name;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHtmlUrl(): ?string
    {
        return $this->htmlUrl;
    }

    /**
     * @param string|null $htmlUrl
     * @return $this
     */
    public function setHtmlUrl(?string $htmlUrl): self
    {
        $this->htmlUrl = $htmlUrl;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'html_url' => $this->html_url,
        ];
    }
}
