<?php
declare(strict_types=1);

namespace App\Tests\integration\History;

use App\Core\Application\Service\History\HistoryItem;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Tests\Stubs\History\History;
use PHPUnit\Framework\TestCase;

/**
 * Class HistoryTest
 * @package App\Tests\integration\History
 */
class HistoryTest extends TestCase
{
    /**
     * @test
     * @description: zapisanie playera, jego ruchu i gry pozwala odczytać go z historii
     * @todo: jednoczesnie ostatni ruch w jednej grze to inny ruch w innej grze
     */
    public function playerMovementForGameShouldBeSaved()
    {
        $history = new History();
        $gameProphecy = $this->prophesize(Game::class);
        $gameProphecy->uuid()->willReturn('0');
        $game = $gameProphecy->reveal();

        $player0 = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), \uniqid());
        $playerX = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), \uniqid());
        $tile1 = new Tile(0, 0);
        $tile2 = new Tile(1, 1);

        $history->saveTurn($player0, $tile1, $game);
        $history->saveTurn($playerX, $tile2, $game);

        $historyItem = $history->lastItem($game);
        self::assertInstanceOf(HistoryItem::class, $historyItem);
        self::assertSame('0', $game->uuid());
        self::assertEquals($playerX, $historyItem->player());
        self::assertEquals($tile2, $historyItem->tile());
        self::assertEquals($game, $historyItem->game());

        $historyItem = $history->getTurn($game, 1);
        self::assertInstanceOf(HistoryItem::class, $historyItem);
        self::assertEquals($player0, $historyItem->player());
        self::assertEquals($tile1, $historyItem->tile());
        self::assertEquals($game, $historyItem->game());

        $tile3 = new Tile(2, 2);
        $gameProphecy2 = $this->prophesize(Game::class);
        $gameProphecy2->uuid()->willReturn('1');
        $game2 = $gameProphecy2->reveal();
        $history->saveTurn($player0, $tile3, $game2);
        $history->saveTurn($player0, $tile1, $game);
        $historyItem = $history->lastItem($game2);
        self::assertSame('1', $game2->uuid());
        self::assertInstanceOf(HistoryItem::class, $historyItem);
        self::assertEquals($player0, $historyItem->player());
        self::assertEquals($tile3, $historyItem->tile());
        self::assertEquals($game2, $historyItem->game());
    }
}
