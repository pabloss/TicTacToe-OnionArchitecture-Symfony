<?php
declare(strict_types=1);

namespace App\Core\Domain\Event;

use App\Core\Domain\Event\Params\ParamsInterface;
use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;

/**
 * Interface EventInterface
 * @package App\Core\Domain\Event
 */
interface EventInterface
{
    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return ParamsInterface
     */
    public function params(): ?ParamsInterface;

    /**
     * @return Game|null
     */
    public function game(): ?Game;

    /**
     * @return Player|null
     */
    public function player(): ?Player;

    /**
     * @return Tile|null
     */
    public function tile(): ?Tile;

    /**
     * @return Board|null
     */
    public function gameBoard(): ?Board;

    /**
     * @return HistoryInterface|null
     */
    public function gameHistory(): ?HistoryInterface;

    /**
     * @return int|null
     */
    public function gameErrors(): ?int;
}
