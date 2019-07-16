<?php
declare(strict_types=1);

namespace App\Core\Application\Command;

use App\Core\Application\Service\History\HistoryInterface;
use App\Core\Application\Service\TurnControl\TurnControl;
use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;

/**
 * Class TakeTileService
 * @package App\Core\Application\Service
 */
class TakeTileService
{
    /** @var Game */
    private $game;

    /** @var \App\Core\Application\Service\History\HistoryInterface */
    private $history;

    /** @var \App\Core\Application\Service\TurnControl\TurnControl */
    private $turnControl;

    /**
     * TakeTileService constructor.
     * @param Game $game
     * @param \App\Core\Application\Service\History\HistoryInterface $history
     * @param \App\Core\Application\Service\TurnControl\TurnControl $turnControl
     */
    public function __construct(Game $game, HistoryInterface $history, TurnControl $turnControl)
    {
        $this->game = $game;
        $this->history = $history;
        $this->turnControl = $turnControl;
    }

    /**
     * @param Player $player
     * @param \App\Core\Domain\Model\TicTacToe\Game\Board\Tile $tile
     * @throws NotAllowedSymbolValue
     */
    public function takeTile(Player $player, Tile $tile)
    {
        $this->turnControl->validateTurn($player, $this->game, $this->history);
        $this->game->board()->mark($tile, $player);
        $this->history->saveTurn($player, $tile, $this->game);
    }
}
