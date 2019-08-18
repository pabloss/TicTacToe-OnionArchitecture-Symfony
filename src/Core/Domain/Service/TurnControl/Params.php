<?php
declare(strict_types=1);

namespace App\Core\Domain\Service\TurnControl;

use App\Core\Domain\Model\TicTacToe\Game\Board\TileInterface;
use App\Core\Domain\Model\TicTacToe\Game\GameInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player\PlayerInterface;
use App\Core\Domain\Service\History\HistoryInterface;

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
