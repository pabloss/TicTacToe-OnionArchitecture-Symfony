<?php
declare(strict_types=1);

namespace App\Core\Domain\Service\History;

use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;

/**
 * Interface HistoryInterface
 * @package App\Core\Domain\Model\TicTacToe\Game
 */
interface HistoryInterface
{
    /**
     * @param Game $game
     * @return HistoryContent
     */
    public function content(Game $game): HistoryContent;

    /**
     * @param Game $game
     * @return HistoryItem|null
     */
    public function lastItem(Game $game): ?HistoryItem;

    /**
     * @param Game $game
     * @return string|null
     */
    public function lastItemPlayerSymbolValue(Game $game): ?string;

    /**
     * @return Symbol
     */
    public function getStartingPlayerSymbol(): Symbol;

    /**
     * @return Symbol
     */
    public function getStartingPlayerSymbolValue(): string;

    /**
     * @param Game $game
     * @return int
     */
    public function length(Game $game): int;

    /**
     * @param Player $player
     * @param Tile $tile
     * @param Game $game
     */
    public function saveTurn(Player $player, Tile $tile, Game $game): void;
}
