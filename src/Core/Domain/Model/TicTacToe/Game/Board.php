<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\Game;

use App\Core\Domain\Model\TicTacToe\ValueObject\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;

/**
 * Class Board
 * @package App\Core\Domain\Model\TicTacToe\Game
 */
class Board
{
    /** @var array */
    private $board;

    /**
     * Board constructor.
     */
    public function __construct()
    {
        $this->board = \array_fill(0, 9, null);
    }

    /**
     * @param Tile $tile
     * @param Player $player
     */
    public function mark(Tile $tile, Player $player): void
    {
        $this->board[$tile->column() + 3 * $tile->row()] = $player;
    }

    /**
     * @return array
     */
    public function &contents(): array
    {
        return $this->board;
    }
}
