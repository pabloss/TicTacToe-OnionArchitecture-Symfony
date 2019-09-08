<?php
declare(strict_types=1);

namespace App\AppCore\DomainServices\TurnControl;

use App\AppCore\DomainModel\Game\Board\TileInterface;
use App\AppCore\DomainModel\Game\GameInterface;
use App\AppCore\DomainModel\Game\Player\PlayerInterface;
use App\AppCore\DomainServices\History\HistoryInterface;

class Params
{
    /** @var PlayerInterface */
    private $player;

    /** @var TileInterface */
    private $tile;

    /** @var GameInterface */
    private $game;

    /** @var HistoryInterface */
    private $history;

    /**
     * Params constructor.
     * @param PlayerInterface $player
     * @param TileInterface $tile
     * @param GameInterface $game
     * @param HistoryInterface $history
     */
    public function __construct(
        PlayerInterface $player,
        TileInterface $tile,
        GameInterface $game,
        HistoryInterface $history
    ) {
        $this->player = $player;
        $this->tile = $tile;
        $this->game = $game;
        $this->history = $history;
    }


    /**
     * @return PlayerInterface
     */
    public function player(): PlayerInterface
    {
        return $this->player;
    }

    /**
     * @return GameInterface
     */
    public function game(): GameInterface
    {
        return $this->game;
    }

    /**
     * @return TileInterface
     */
    public function tile(): TileInterface
    {
        return $this->tile;
    }

    /**
     * @return HistoryInterface
     */
    public function history(): HistoryInterface
    {
        return $this->history;
    }
}
