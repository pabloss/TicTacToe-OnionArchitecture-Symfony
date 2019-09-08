<?php
declare(strict_types=1);

namespace App\Tests\integration\Core\Domain\Service\History;

use App\AppCore\DomainModel\Game\Board\Tile;
use App\AppCore\DomainModel\Game\Game;
use App\AppCore\DomainModel\Game\Player\Player;
use App\AppCore\DomainModel\Game\Player\Symbol;
use App\Tests\Stubs\History\History;
use PHPUnit\Framework\TestCase;

class HistoryTest extends TestCase
{
    /**
     * @test
     */
    public function get_last_turn()
    {
        // Given
        $history = new History();

        $gameProphecy = $this->prophesize(Game::class);
        $tileProphecy = $this->prophesize(Tile::class);

        $playerProphecy = $this->prophesize(Player::class);
        $gameProphecy->uuid()->willReturn('0');

        // When
        $history->saveTurn(
            $playerProphecy->reveal(),
            $tileProphecy->reveal(),
            $gameProphecy->reveal()
        );

        // Then
        $historyItem = $history->lastItem($gameProphecy->reveal());
        self::assertEquals($gameProphecy->reveal(), $historyItem->game());
        self::assertEquals($playerProphecy->reveal(), $historyItem->player());
        self::assertEquals($tileProphecy->reveal(), $historyItem->tile());
    }

    /**
     * @test
     */
    public function sequence_of_sets_below_limit_should_take_correct_length()
    {
        // Given
        $history = new History();

        $gameProphecy = $this->prophesize(Game::class);
        $tileProphecy = $this->prophesize(Tile::class);

        $player0Prophecy = $this->prophesize(Player::class);
        $player1Prophecy = $this->prophesize(Player::class);

        // When
        $gameProphecy->uuid()->willReturn('0');
        $player0Prophecy->uuid()->willReturn('0');
        $player1Prophecy->uuid()->willReturn('1');

        $history->saveTurn(
            $player0Prophecy->reveal(),
            $tileProphecy->reveal(),
            $gameProphecy->reveal()
        );
        $history->saveTurn(
            $player1Prophecy->reveal(),
            $tileProphecy->reveal(),
            $gameProphecy->reveal()
        );

        // Then
        self::assertEquals(2, $history->length($gameProphecy->reveal()));
    }

    /**
     * @test
     */
    public function history_should_return_contents()
    {
        // Given
        $history = new History();

        $expectedContent = [];
        $gameProphecy = $this->prophesize(Game::class);
        $tileProphecy = $this->prophesize(Tile::class);

        $gameProphecy->uuid()->willReturn('0');

        $players = [];
        $players[] = $this->prophesize(Player::class);
        $players[] = $this->prophesize(Player::class);
        $players[0]->uuid()->willReturn('0');
        $players[1]->uuid()->willReturn('1');

        // When
        for ($i = 0; $i < History::LIMIT; $i++) {
            $tileProphecy->row()->willReturn(rand(0, 2));
            $tileProphecy->column()->willReturn(rand(0, 2));
            $value = new \App\AppCore\DomainModel\History\HistoryItem(
                $players[$i % 2]->reveal(),
                $tileProphecy->reveal(),
                $gameProphecy->reveal()
            );
            $expectedContent[$history->length($gameProphecy->reveal()) % History::LIMIT] = $value;
            $history->saveTurn(
                $players[$i % 2]->reveal(),
                $tileProphecy->reveal(),
                $gameProphecy->reveal()
            );
        }

        // Then
        self::assertEquals(new \App\AppCore\DomainModel\History\HistoryContent($expectedContent), $history->content($gameProphecy->reveal()));

        $randIndexInHistory = History::LIMIT - rand(1, History::LIMIT);
        $randomExpectedHistoryItem = (new \App\AppCore\DomainModel\History\HistoryContent($expectedContent))->getArrayCopy()[$randIndexInHistory];
        $randomActualHistoryItem = $history->content($gameProphecy->reveal())->getArrayCopy()[$randIndexInHistory];

        self::assertEquals($randomExpectedHistoryItem, $randomActualHistoryItem);
    }

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

        $player0 = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), uniqid());
        $playerX = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), uniqid());
        $tile1 = new Tile(0, 0);
        $tile2 = new Tile(1, 1);

        $history->saveTurn($player0, $tile1, $game);
        $history->saveTurn($playerX, $tile2, $game);

        $historyItem = $history->lastItem($game);
        self::assertInstanceOf(\App\AppCore\DomainModel\History\HistoryItem::class, $historyItem);
        self::assertSame('0', $game->uuid());
        self::assertEquals($playerX, $historyItem->player());
        self::assertEquals($tile2, $historyItem->tile());
        self::assertEquals($game, $historyItem->game());

        $historyItem = $history->getTurn($game, 1);
        self::assertInstanceOf(\App\AppCore\DomainModel\History\HistoryItem::class, $historyItem);
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
        self::assertInstanceOf(\App\AppCore\DomainModel\History\HistoryItem::class, $historyItem);
        self::assertEquals($player0, $historyItem->player());
        self::assertEquals($tile3, $historyItem->tile());
        self::assertEquals($game2, $historyItem->game());
    }
}
