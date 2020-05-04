<?php

namespace App\Entity;

class Action
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
     * @return array
     */
    public function toArray(): array
    {
        return [
            "name" => $this->name,
            "html_url" => $this->htmlUrl
        ];
    }

    /**
     * Categorie factory from Array
     *
     * @param array $data
     * @return Action
     */
    public static function fromArray(array $data): self
    {
        $action = new Action();
        $action->setName($data['name'] ?? null);
        $action->setHtmlUrl($data['html_url'] ?? null);
        return $action;
    }
}
