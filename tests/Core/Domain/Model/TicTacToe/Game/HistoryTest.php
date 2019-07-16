<?php
declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\TicTacToe;

use App\Core\Application\Service\History\HistoryContent;
use App\Core\Application\Service\History\HistoryItem;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
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
            $tileProphecy->row()->willReturn(\rand(0,2));
            $tileProphecy->column()->willReturn(\rand(0,2));
            $value = new HistoryItem(
                $players[$i%2]->reveal(),
                $tileProphecy->reveal(),
                $gameProphecy->reveal()
            );
            $expectedContent[$history->length($gameProphecy->reveal()) % History::LIMIT] = $value;
            $history->saveTurn(
                $players[$i%2]->reveal(),
                $tileProphecy->reveal(),
                $gameProphecy->reveal()
            );
        }

        // Then
        self::assertEquals(new HistoryContent($expectedContent), $history->content($gameProphecy->reveal()));

        $randIndexInHistory = History::LIMIT - \rand(1, History::LIMIT);
        $randomExpectedHistoryItem = (new HistoryContent($expectedContent))->getArrayCopy()[$randIndexInHistory];
        $randomActualHistoryItem = $history->content($gameProphecy->reveal())->getArrayCopy()[$randIndexInHistory];

        self::assertEquals($randomExpectedHistoryItem, $randomActualHistoryItem);
    }
}
