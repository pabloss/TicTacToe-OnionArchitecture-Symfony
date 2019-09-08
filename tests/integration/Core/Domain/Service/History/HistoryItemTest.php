<?php
declare(strict_types=1);

namespace App\Tests\integration\Core\Domain\Service\History;

use App\AppCore\DomainModel\Game\Board\Tile;
use App\AppCore\DomainModel\Game\Game;
use App\AppCore\DomainModel\Game\Player\Player;
use App\AppCore\DomainModel\Game\Player\Symbol;
use PHPUnit\Framework\TestCase;

/**
 * Class HistoryItemTest
 * @package App\Tests\unit\Core\Domain\Service
 */
class HistoryItemTest extends TestCase
{
    /**
     * @test
     */
    public function saveGamePlayerTile()
    {
        $gameProphecy = $this->prophesize(Game::class);
        $game = $gameProphecy->reveal();
        $playerX = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), uniqid());
        $tile1 = new Tile(0, 0);
        $historyItem = new \App\AppCore\DomainModel\History\HistoryItem($playerX, $tile1, $game);

        self::assertEquals($playerX, $historyItem->player());
        self::assertEquals($tile1, $historyItem->tile());
        self::assertEquals($game, $historyItem->game());
    }
}
