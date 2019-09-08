<?php
declare(strict_types=1);

namespace App\AppCore\DomainModel\History;

use App\AppCore\DomainModel\Game\Board\TileInterface;
use App\AppCore\DomainModel\Game\GameInterface;
use App\AppCore\DomainModel\Game\Player\PlayerInterface;
use App\AppCore\DomainModel\Game\Player\Symbol;

/**
 * Interface HistoryInterface
 * @package App\Core\Domain\Model\TicTacToe\Game
 */
interface HistoryInterface
{
    /**
     * @param GameInterface $game
     * @return HistoryContent
     */
    public function content(GameInterface $game): HistoryContent;

    /**
     * @param GameInterface $game
     * @return HistoryItem|null
     */
    public function lastItem(GameInterface $game): ?HistoryItem;

    /**
     * @param GameInterface $game
     * @return string|null
     */
    public function lastItemPlayerSymbolValue(GameInterface $game): ?string;

    /**
     * @return Symbol
     */
    public function getStartingPlayerSymbol(): Symbol;

    /**
     * @return Symbol
     */
    public function getStartingPlayerSymbolValue(): string;

    /**
     * @param GameInterface $game
     * @return int
     */
    public function length(GameInterface $game): int;

    /**
     * @param PlayerInterface $player
     * @param TileInterface $tile
     * @param GameInterface $game
     */
    public function saveTurn(PlayerInterface $player, TileInterface $tile, GameInterface $game): void;
}
