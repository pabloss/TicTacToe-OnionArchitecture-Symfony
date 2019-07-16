<?php
declare(strict_types=1);

namespace App\Tests\unit\History;

use App\Core\Application\Service\History\HistoryItem;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use PHPUnit\Framework\TestCase;

/**
 * Class HistoryItemTest
 * @package App\Tests\unit\History
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
        $playerX = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), \uniqid());
        $tile1 = new Tile(0, 0);
        $historyItem = new HistoryItem($playerX, $tile1, $game);

        self::assertEquals($playerX, $historyItem->player());
        self::assertEquals($tile1, $historyItem->tile());
        self::assertEquals($game, $historyItem->game());
    }
}
