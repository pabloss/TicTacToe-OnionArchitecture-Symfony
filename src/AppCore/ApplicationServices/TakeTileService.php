<?php
declare(strict_types=1);

namespace App\AppCore\ApplicationServices;

use App\AppCore\DomainModel\Game\Board\TileInterface;
use App\AppCore\DomainModel\Game\GameInterface;
use App\AppCore\DomainModel\Game\Player\PlayerInterface;
use App\AppCore\DomainModel\History\HistoryInterface;
use App\AppCore\DomainServices\TurnControl\Params;
use App\AppCore\DomainServices\TurnControl\TurnControl;

/**
 * Class TakeTileService
 * @package App\AppCore\DomainServices
 */
class TakeTileService
{
    /** @var GameInterface */
    private $game;

    /** @var \App\AppCore\DomainModel\History\HistoryInterface */
    private $history;

    /** @var TurnControl */
    private $turnControl;

    /**
     * TakeTileService constructor.
     * @param GameInterface $game
     * @param \App\AppCore\DomainModel\History\HistoryInterface $history
     * @param TurnControl $turnControl
     */
    public function __construct(GameInterface $game, HistoryInterface $history, TurnControl $turnControl)
    {
        $this->game = $game;
        $this->history = $history;
        $this->turnControl = $turnControl;
    }

    /**
     * @param PlayerInterface $player
     * @param TileInterface $tile
     */
    public function takeTile(PlayerInterface $player, TileInterface $tile)
    {
        $this->turnControl->validateTurn(new Params($player, $tile, $this->game, $this->history));
        $this->game->board()->mark($tile, $player);
        $this->history->saveTurn($player, $tile, $this->game);
    }
}
