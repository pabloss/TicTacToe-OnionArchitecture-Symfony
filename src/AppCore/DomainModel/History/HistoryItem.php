<?php
declare(strict_types=1);

namespace App\AppCore\DomainModel\History;

use App\AppCore\DomainModel\Game\Board\Tile;
use App\AppCore\DomainModel\Game\Game;
use App\AppCore\DomainModel\Game\Player\Player;


/**
 * Class HistoryItem
 * @package App\Tests\Stubs\History
 */
class HistoryItem
{
    /** @var Player */
    private $player;

    /** @var \App\AppCore\DomainModel\Game\Board\Tile */
    private $tile;

    /** @var Game */
    private $game;

    /**
     * HistoryItem constructor.
     * @param Player $player
     * @param \App\AppCore\DomainModel\Game\Board\Tile $tile
     * @param Game $game
     */
    public function __construct(Player $player = null, Tile $tile = null, Game $game = null)
    {
        $this->player = $player;
        $this->tile = $tile;
        $this->game = $game;
    }

    /**
     * @return Player
     */
    public function player(): ?Player
    {
        return $this->player;
    }

    /**
     * @return \App\AppCore\DomainModel\Game\Board\Tile
     */
    public function tile(): ?Tile
    {
        return $this->tile;
    }

    /**
     * @return array
     */
    public function getTileArray(): array
    {
        return [$this->tile->row(), $this->tile->column()];
    }

    /**
     * @return Game
     */
    public function game(): ?Game
    {
        return $this->game;
    }
}
