<?php
declare(strict_types=1);

namespace App\Core\Domain\Event\Params;

use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;

class Params implements ParamsInterface
{
    /** @var Player */
    private $player;

    /** @var Tile */
    private $tile;

    /** @var Game */
    private $game;

    /**
     * Params constructor.
     * @param \App\Core\Domain\Model\TicTacToe\Game\Player $player
     * @param Tile $tile
     * @param Game $game
     */
    public function __construct(Player $player, Tile $tile, Game $game)
    {
        $this->player = $player;
        $this->tile = $tile;
        $this->game = $game;
    }

    public function player(): Player
    {
        return $this->player;
    }

    public function tile(): Tile
    {
        return $this->tile;
    }

    public function game(): Game
    {
        return $this->game;
    }

}
