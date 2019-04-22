<?php
declare(strict_types=1);

namespace App\Core\Domain\Event\Params;

use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;

interface ParamsInterface
{
    public function player(): Player;
    public function tile(): Tile;
    public function game(): Game;
}
