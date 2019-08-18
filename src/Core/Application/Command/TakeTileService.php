<?php
declare(strict_types=1);

namespace App\Core\Application\Command;

use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Service\History\HistoryInterface;
use App\Core\Domain\Service\TurnControl\Params;
use App\Core\Domain\Service\TurnControl\TurnControl;

/**
 * Class TakeTileService
 * @package App\Core\Domain\Service
 */
class TakeTileService
{
    /** @var Game */
    private $game;

    /** @var HistoryInterface */
    private $history;

    /** @var TurnControl */
    private $turnControl;

    /**
     * TakeTileService constructor.
     * @param Game $game
     * @param HistoryInterface $history
     * @param TurnControl $turnControl
     */
    public function __construct(Game $game, HistoryInterface $history, TurnControl $turnControl)
    {
        $this->game = $game;
        $this->history = $history;
        $this->turnControl = $turnControl;
    }

    /**
     * @param Player $player
     * @param Tile $tile
     * @throws NotAllowedSymbolValue
     */
    public function takeTile(Player $player, Tile $tile)
    {
        $this->turnControl->validateTurn(new Params($player, $tile, $this->game, $this->history));
        $this->game->board()->mark($tile, $player);
        $this->history->saveTurn($player, $tile, $this->game);
    }
}
