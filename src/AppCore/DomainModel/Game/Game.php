<?php
declare(strict_types=1);

namespace App\AppCore\DomainModel\Game;

use App\Core\Domain\Model\TicTacToe\Game\Board\Board;

/**
 * Class Game
 * @package App\Core\Domain\Model\TicTacToe\Game
 */
class Game implements GameInterface
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
     * @param string $uuid
     */
    public function __construct(Board $board, string $uuid)
    {
        $this->board = $board;
        $this->uuid = $uuid;
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
        return $this->uuid;
    }
}
