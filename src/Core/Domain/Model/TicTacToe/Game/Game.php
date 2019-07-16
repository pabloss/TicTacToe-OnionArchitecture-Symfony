<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\Game;

use App\Core\Domain\Model\TicTacToe\Game\Board\Board;

/**
 * Class Game
 * @package App\Core\Domain\Model\TicTacToe\Game
 */
class Game
{
    /**
     * @var Board
     */
    private $board;


    /** @var string $uuid */
    private $uuid;


    /**
     * Game constructor.
     * @param Board $board
     */
    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    /**
     * @return Board
     */
    public function board(): Board
    {
        return $this->board;
    }

    public function uuid(): string
    {
        return '1';
//        return $this->uuid;
    }
}
