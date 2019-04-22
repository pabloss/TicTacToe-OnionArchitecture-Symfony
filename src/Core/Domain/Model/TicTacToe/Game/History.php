<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\Game;

use App\Core\Application\History\HistoryContent;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use App\Tests\Stubs\History\HistoryItem;

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
     * @var
     */
    private $lastTurn;

    /**
     * History constructor.
     */
    public function __construct()
    {
        $this->startingPlayerSymbol = new Symbol(Symbol::PLAYER_X_SYMBOL);
    }

    /**
     * @return array
     */
    public function getLast(): array
    {
        return \end($this->timeLine);
    }

    /**
     * @param Game $game
     * @return array
     */
    public function &content(Game $game): HistoryContent
    {
        return new HistoryContent($this->timeLine[$game->uuid()]);
    }

    /**
     * @param Game $game
     * @return mixed
     */
    public function getLastTurn(Game $game): ?HistoryItem
    {
        return $this->timeLine[$game->uuid()][($this->length($game) - 1) % self::LIMIT] ?? null;
    }

    /**
     * @return Symbol
     */
    public function getStartingPlayerSymbol(): Symbol
    {
        return $this->startingPlayerSymbol;
    }


    /**
     * @param Player $player
     * @param Tile $tile
     * @param Game $game
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
     */
    public function saveTurn(Player $player, Tile $tile, Game $game): void
    {
        $this->timeLine[$game->uuid()][$this->length($game) % self::LIMIT] = new HistoryItem($player, $tile, $game);
    }

    /**
     * @param Tile $tile
     */
    public function saveTurnToHistory(Tile $tile): void
    {
        $this->set([$tile->row(), $tile->column()]);
    }

    /**
     * @param $value
     */
    public function set($value): void
    {
        $this->timeLine[$this->length() % self::LIMIT] = $value;
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
}
