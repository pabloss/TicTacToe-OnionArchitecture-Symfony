<?php
declare(strict_types=1);

namespace App\AppCore\DomainModel\History;

use App\AppCore\DomainModel\Game\Board\TileInterface;
use App\AppCore\DomainModel\Game\GameInterface;
use App\AppCore\DomainModel\Game\Player\PlayerInterface;

/**
 * Class HistoryItem
 * @package App\Tests\Stubs\History
 */
class HistoryItem
{
    /** @var PlayerInterface */
    private $player;

    /** @var TileInterface */
    private $tile;

    /** @var GameInterface */
    private $game;

    /**
     * HistoryItem constructor.
     * @param PlayerInterface $player
     * @param TileInterface $tile
     * @param GameInterface $game
     */
    public function __construct(PlayerInterface $player = null, TileInterface $tile = null, GameInterface $game = null)
    {
        $this->player = $player;
        $this->tile = $tile;
        $this->game = $game;
    }

    /**
     * @return PlayerInterface
     */
    public function player(): ?PlayerInterface
    {
        return $this->player;
    }

    /**
     * @return TileInterface
     */
    public function tile(): ?TileInterface
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
     * @return GameInterface
     */
    public function game(): ?GameInterface
    {
        return $this->game;
    }
}
