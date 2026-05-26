<?php

namespace App\Model;

use App\Service\Config;

class Athlete
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $sportName = null;
    private ?int $age = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Athlete
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Athlete
    {
        $this->name = $name;

        return $this;
    }

    public function getSportName(): ?string
    {
        return $this->sportName;
    }

    public function setSportName(?string $sportName): Athlete
    {
        $this->sportName = $sportName;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): Athlete
    {
        $this->age = $age;

        return $this;
    }

    public static function fromArray($array): Athlete
    {
        $athlete = new self();
        $athlete->fill($array);

        return $athlete;
    }

    public function fill($array): Athlete
    {
        if (isset($array['id']) && ! $this->getId()) {
            $this->setId((int) $array['id']);
        }

        if (isset($array['name'])) {
            $this->setName($array['name']);
        }

        if (isset($array['sport_name'])) {
            $this->setSportName($array['sport_name']);
        }

        if (isset($array['age'])) {
            $this->setAge((int) $array['age']);
        }

        return $this;
    }

    public static function findAll(): array
    {
        $pdo = new \PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));

        $sql = 'SELECT * FROM athlete';
        $statement = $pdo->prepare($sql);
        $statement->execute();

        $athletes = [];
        $athletesArray = $statement->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($athletesArray as $athleteArray) {
            $athletes[] = self::fromArray($athleteArray);
        }

        return $athletes;
    }

    public static function find($id): ?Athlete
    {
        $pdo = new \PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));

        $sql = 'SELECT * FROM athlete WHERE id = :id';
        $statement = $pdo->prepare($sql);
        $statement->execute([
            ':id' => $id,
        ]);

        $athleteArray = $statement->fetch(\PDO::FETCH_ASSOC);

        if (! $athleteArray) {
            return null;
        }

        return Athlete::fromArray($athleteArray);
    }

    public function save(): void
    {
        $pdo = new \PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));

        if (! $this->getId()) {
            $sql = 'INSERT INTO athlete (name, sport_name, age) VALUES (:name, :sport_name, :age)';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':name' => $this->getName(),
                ':sport_name' => $this->getSportName(),
                ':age' => $this->getAge(),
            ]);

            $this->setId((int) $pdo->lastInsertId());
        } else {
            $sql = 'UPDATE athlete SET name = :name, sport_name = :sport_name, age = :age WHERE id = :id';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':name' => $this->getName(),
                ':sport_name' => $this->getSportName(),
                ':age' => $this->getAge(),
                ':id' => $this->getId(),
            ]);
        }
    }

    public function delete(): void
    {
        $pdo = new \PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));

        $sql = 'DELETE FROM athlete WHERE id = :id';
        $statement = $pdo->prepare($sql);
        $statement->execute([
            ':id' => $this->getId(),
        ]);

        $this->setId(null);
        $this->setName(null);
        $this->setSportName(null);
        $this->setAge(null);
    }
}