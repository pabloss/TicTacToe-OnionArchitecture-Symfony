<?php
declare(strict_types=1);

namespace App\Core\Application\Service;

use App\Core\Application\Validation\TurnControl;
use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;

/**
 * Class TakeTileService
 * @package App\Core\Application\Service
 */
class TakeTileService
{
    /** @var Game */
    private $game;

    /** @var HistoryInterface */
    private $history;

    /**
     * TakeTileService constructor.
     * @param Game $game
     * @param HistoryInterface $history
     */
    public function __construct(Game $game, HistoryInterface $history)
    {
        $this->game = $game;
        $this->history = $history;
    }

    /**
     * @param Player $player
     * @param Tile $tile
     * @throws NotAllowedSymbolValue
     */
    public function takeTile(Player $player, Tile $tile)
    {
        $this->game->board()->mark($tile, $player);
        $this->history->saveTurn($player, $tile, $this->game);
        TurnControl::validateTurn($player, $this->game, $this->history);
    }

    /**
     * @param int $error
     * @return bool
     */
    public function hasError(int $error): bool
    {
        return !!($error & $this->game->errors());
    }
}
