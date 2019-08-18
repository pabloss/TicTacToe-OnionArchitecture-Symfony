<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\Game\Player;

interface PlayerInterface
{
    public function symbolValue(): string;
    public function uuid(): string;
}
