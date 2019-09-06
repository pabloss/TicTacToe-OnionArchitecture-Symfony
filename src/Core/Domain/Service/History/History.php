<?php
declare(strict_types=1);

namespace App\Core\Domain\Service\History;

use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\GameInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use function count;
use function end;

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
        return end($this->timeLine);
    }

    /**
     * @param Game $game
     * @return array
     */
    public function content(GameInterface $game): HistoryContent
    {
        return new HistoryContent($this->timeLine[$game->uuid()] ?? []);
    }

    /**
     * @param Game $game
     * @return string|null
     */
    public function lastItemPlayerSymbolValue(GameInterface $game): ?string
    {
        return (null !== $this->lastItem($game)) ? $this->lastItem($game)->player()->symbol()->value(): null;
    }

    /**
     * @param Game $game
     * @return mixed
     */
    public function lastItem(GameInterface $game): ?HistoryItem
    {
        return $this->timeLine[$game->uuid()][($this->length($game) - 1) % self::LIMIT] ?? null;
    }

    /**
     * @param Game $game
     * @return int
     */
    public function length(Game $game): int
    {
        if (isset($this->timeLine[$game->uuid()])) {
            return count($this->timeLine[$game->uuid()]);
        }

        return 0;
    }

    /**
     * @return string
     */
    public function getStartingPlayerSymbolValue(): string
    {
        return $this->getStartingPlayerSymbol()->value();
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
}