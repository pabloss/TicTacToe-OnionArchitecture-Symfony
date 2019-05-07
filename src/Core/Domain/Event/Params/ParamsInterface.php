<?php
declare(strict_types=1);

namespace App\Core\Domain\Event\Params;

use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use App\Tests\Stubs\History\History;

/**
 * Interface ParamsInterface
 * @package App\Core\Domain\Event\Params
 */
interface ParamsInterface
{
    /**
     * @return Player
     */
    public function player(): Player;

    /**
     * @return Tile
     */
    public function tile(): Tile;

    /**
     * @return Game
     */
    public function game(): Game;
}
