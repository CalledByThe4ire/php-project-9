<?php

namespace App;

class Url
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $created_at = null;

    public static function fromArray(array $urlData): Url
    {
        ['name' => $name] = $urlData;
        $url = new Url();
        $url->setName($name);

        return $url;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setCreatedAt(?string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function exists(): bool
    {
        return !is_null($this->getId());
    }
}