<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\Game;

use App\Core\Application\History\HistoryContent;
use App\Core\Application\History\HistoryItem;
use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;

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
    protected $timeLine = [];

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
     * @return array
     * todo: remove the method
     */
    public function getLast(): array
    {
        return \end($this->timeLine);
    }

    /**
     * @param Game $game
     * @return array
     */
    public function content(Game $game): HistoryContent
    {
        return new HistoryContent($this->timeLine[$game->uuid()]);
    }

    /**
     * @param Game $game
     * @return mixed
     */
    public function lastItem(Game $game): ?HistoryItem
    {
        return $this->timeLine[$game->uuid()][($this->length($game) - 1) % self::LIMIT] ?? null;
    }

    /**
     * @param Game $game
     * @return string|null
     */
    public function lastItemPlayerSymbolValue(Game $game): ?string
    {
        return $this->lastItem($game)->player()->symbol()->value();
    }

    /**
     * @return Symbol
     */
    public function getStartingPlayerSymbol(): Symbol
    {
        return $this->startingPlayerSymbol;
    }

    /**
     * @return string
     */
    public function getStartingPlayerSymbolValue(): string
    {
        return $this->getStartingPlayerSymbol()->value();
    }

    /**
     * @param Player $player
     * @param \App\Core\Domain\Model\TicTacToe\Game\Board\Tile $tile
     * @param Game $game
     * @throws NotAllowedSymbolValue
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
     * todo: remove the method
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
