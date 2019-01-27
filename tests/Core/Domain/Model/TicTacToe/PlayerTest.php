<?php
declare(strict_types=1);

namespace AppTests\Core\Domain\Model\TicTacToe;

use App\Core\Domain\Event\EventManager;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\History;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player;
use App\Core\Domain\Service\PlayersFactory;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    /**
     * @test
     */
    public function player_has_symbol()
    {
        $symbol = new \App\Core\Domain\Model\TicTacToe\ValueObject\Symbol(\App\Core\Domain\Model\TicTacToe\ValueObject\Symbol::PLAYER_X_SYMBOL);

        $player = new Player($symbol, EventManager::getInstance());
        self::assertEquals($symbol, $player->symbol());
    }
}
