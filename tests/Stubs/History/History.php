<?php
declare(strict_types=1);

namespace App\Tests\Stubs\History;

use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;

/**
 * Class History
 * @package App\Core\Domain\Model\TicTacToe\Game
 */
class History implements HistoryInterface
{

    const LIMIT = 9;

    /**
     * @var array
     */
    private $timeLine = [];

    /**
     * @var Symbol
     */
    private $startingPlayerSymbol;

    /**
     * History constructor.
     */
    public function __construct()
    {
        $this->startingPlayerSymbol = new Symbol(Symbol::PLAYER_X_SYMBOL);
    }


    /**
     * @param Game $game
     * @return array
     */
    public function &content(Game $game): array
    {
        return $this->timeLine[$game->uuid()];
    }

    /**
     * @param Game $game
     * @return mixed
     */
    public function getLastTurn(Game $game): ?HistoryItem
    {
        return $this->timeLine[$game->uuid()][($this->length($game) - 1) % self::LIMIT] ?? null;
    }

    public function getTurn($game, $index)
    {
        return $this->timeLine[$game->uuid()][($this->length($game) - 1 - $index) % self::LIMIT];
    }

    /**
     * @return Symbol
     */
    public function getStartingPlayerSymbol(): Symbol
    {
        return $this->startingPlayerSymbol;
    }


    /**
     * @param Game $game
     * @return int
     */
    public function length(Game $game): int
    {
        if (isset($this->timeLine[$game->uuid()])) {
            return \count($this->timeLine[$game->uuid()]);
        }

        return 0;
    }

    /**
     * @param Player $player
     * @param Tile $tile
     * @param Game $game
     */
    public function saveTurn(Player $player, Tile $tile, Game $game): void
    {
        $this->timeLine[$game->uuid()][$this->length($game) % self::LIMIT] = new HistoryItem($player, $tile, $game);
    }
}
