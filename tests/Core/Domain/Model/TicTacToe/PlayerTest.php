<?php
declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\TicTacToe;

use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    /**
     * @test
     */
    public function player_has_symbol()
    {
        $symbol = new Symbol(Symbol::PLAYER_X_SYMBOL);

        $player = new Player($symbol, uniqid());
        self::assertEquals($symbol, $player->symbol());
    }
}
