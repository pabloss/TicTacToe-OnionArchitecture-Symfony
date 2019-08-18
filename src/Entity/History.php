<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HistoryRepository")
 * @codeCoverageIgnore
 */
class History implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $createdAt;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;
    /**
     * @ORM\Column(type="string")
     */
    private $gameUuid;
    /**
     * @ORM\Column(type="string")
     */
    private $playerUuid;
    /**
     * @ORM\Column(type="string")
     */
    private $playerSymbol;
    /**
     * @ORM\Column(type="json")
     */
    private $tile;

    public function getValueObject(): object
    {
        // TODO: Implement getValueObject() method.
    }

    public function setValueObject(object $valueObject): EntityInterface
    {
        // TODO: Implement setValueObject() method.
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        if (null === $updatedAt) {
            $this->createdAt = new DateTime();
        }
        return $this;
    }

    public function getGameUuid(): ?string
    {
        return $this->gameUuid;
    }

    public function setGameUuid(string $gameUuid): self
    {
        $this->gameUuid = $gameUuid;

        return $this;
    }

    public function getPlayerUuid(): ?string
    {
        return $this->playerUuid;
    }

    public function setPlayerUuid(string $playerUuid): self
    {
        $this->playerUuid = $playerUuid;

        return $this;
    }

    public function getTile(): ?array
    {
        return $this->tile;
    }

    public function setTile(array $tile): self
    {
        $this->tile = $tile;

        return $this;
    }

    public function getPlayerSymbol(): ?string
    {
        return $this->playerSymbol;
    }

    public function setPlayerSymbol(string $playerSymbol): self
    {
        $this->playerSymbol = $playerSymbol;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
