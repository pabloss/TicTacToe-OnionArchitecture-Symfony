<?php
declare(strict_types=1);

namespace AppTests\Core\Domain\Model\TicTacToe;

use App\Core\Application\EventSubscriber\TakeTileEventSubscriber;
use App\Core\Domain\Event\EventManager;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    /**
     * @test
     */
    public function player_has_symbol()
    {
        $symbol = new \App\Core\Domain\Model\TicTacToe\ValueObject\Symbol(\App\Core\Domain\Model\TicTacToe\ValueObject\Symbol::PLAYER_X_SYMBOL);

        $player = new Player($symbol, EventManager::getInstance([TakeTileEventSubscriber::class]));
        self::assertEquals($symbol, $player->symbol());
    }
}
