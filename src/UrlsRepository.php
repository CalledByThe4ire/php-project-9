<?php

namespace App;

use Carbon\Carbon;

class UrlsRepository implements UrlsRepositoryInterface
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getEntities(): array
    {
        $urls = [];
        $sql = "SELECT * FROM urls";
        $stmt = $this->conn->query($sql);

        while ($row = $stmt->fetch()) {
            $url = Url::fromArray($row);
            $url->setId($row['id']);
            $url->setCreatedAt(Carbon::parse($row['created_at'])->toDateTimeString());
            $urls[] = $url;
        }

        return $urls;
    }

    public function find(int $id): ?Url
    {
        $sql = "SELECT * FROM urls WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        if ($row = $stmt->fetch()) {
            $url = Url::fromArray($row);
            $url->setId($row['id']);
            $url->setCreatedAt(Carbon::parse($row['created_at'])->toDateTimeString());

            return $url;
        }

        return null;
    }

    public function create(Url $url): void
    {
        $sql = "INSERT INTO urls (name) VALUES (:name)";

        $stmt = $this->conn->prepare($sql);
        $name = $url->getName();
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        $id = (int)$this->conn->lastInsertId();
        $url->setId($id);
    }

    public function delete(int $id): void
    {
        $sql = "DELETE FROM urls WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        $stmt->execute([$id]);
    }
}