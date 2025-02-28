<?php

namespace App;

interface UrlsRepositoryInterface
{
    public function getEntities(): array;

    public function find(int $id): ?Url;

    public function delete(int $id): void;

    public function create(Url $url): void;
}