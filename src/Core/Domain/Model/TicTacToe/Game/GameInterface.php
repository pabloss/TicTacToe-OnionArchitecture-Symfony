<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\Game;

interface GameInterface
{
    public function uuid(): string;
}
