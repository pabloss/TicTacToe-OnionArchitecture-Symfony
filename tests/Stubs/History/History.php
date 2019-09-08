<?php
declare(strict_types=1);

namespace App\Tests\Stubs\History;

use App\AppCore\DomainModel\Game\Board\Tile;
use App\AppCore\DomainModel\Game\Board\TileInterface;
use App\AppCore\DomainModel\Game\GameInterface;
use App\AppCore\DomainModel\Game\Player\PlayerInterface;
use App\AppCore\DomainModel\Game\Player\Symbol;
use App\AppCore\DomainModel\History\HistoryContent;
use App\AppCore\DomainModel\History\HistoryItem;

/**
 * Class History
 * @package App\Core\Domain\Model\TicTacToe\Game
 */
class History implements \App\AppCore\DomainModel\History\HistoryInterface
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
     * @param GameInterface $game
     * @return HistoryContent
     */
    public function content(GameInterface $game): \App\AppCore\DomainModel\History\HistoryContent
    {
        return new HistoryContent($this->timeLine[$game->uuid()] ?? []);
    }

    /**
     * @param GameInterface $game
     * @return string|null
     */
    public function lastItemPlayerSymbolValue(GameInterface $game): ?string
    {
        return (null !== $this->lastItem($game)) ? $this->lastItem($game)->player()->symbol()->value(): null;
    }

    /**
     * @param GameInterface $game
     * @return mixed
     */
    public function lastItem(GameInterface $game): ?HistoryItem
    {
        return $this->timeLine[$game->uuid()][($this->length($game) - 1) % self::LIMIT] ?? null;
    }

    /**
     * @param GameInterface $game
     * @return int
     */
    public function length(GameInterface $game): int
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
     * @param PlayerInterface $player
     * @param TileInterface $tile
     * @param GameInterface $game
     */
    public function saveTurn(PlayerInterface $player, TileInterface $tile, GameInterface $game): void
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

    public function getTurn($game, $index)
    {
        return $this->timeLine[$game->uuid()][($this->length($game) - 1 - $index) % self::LIMIT];
    }

}
