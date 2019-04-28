<?php
declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\TicTacToe;

use App\Core\Application\History\HistoryContent;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use App\Tests\Stubs\History\History;
use App\Tests\Stubs\History\HistoryItem;
use PHPUnit\Framework\TestCase;

class HistoryTest extends TestCase
{
    /**
     * @test
     */
    public function history_should_record_values()
    {
        $history = new History();
        list($playerProphecy, $gameProphecy, $tileProphecy) = $this->setUpDependencies();
        $this->configureProphecies($playerProphecy, $gameProphecy, '0', '0');
        $history->saveTurn(
            $playerProphecy->reveal(),
            $tileProphecy->reveal(),
            $gameProphecy->reveal()
        );
        $historyItem = $history->getLastTurn($gameProphecy->reveal());
        self::assertEquals($gameProphecy->reveal(), $historyItem->game());
        self::assertEquals($playerProphecy->reveal(), $historyItem->player());
        self::assertEquals($tileProphecy->reveal(), $historyItem->tile());
    }

    /**
     * @return array
     */
    private function setUpDependencies($gameUuid, $playerUuid): array
    {
        $playerProphecy = $this->prophesize(Player::class);
        $gameProphecy = $this->prophesize(Game::class);
        $tileProphecy = $this->prophesize(Tile::class);
        $gameProphecy->uuid()->willReturn($gameUuid);
        $playerProphecy->getUuid()->willReturn($playerUuid);

        return array($playerProphecy, $gameProphecy, $tileProphecy);
    }

    /**
     * @param $playerProphecy
     * @param $gameProphecy
     */
    private function configureProphecies($playerProphecy, $gameProphecy, $first, $second): void
    {
        $playerProphecy->getUuid()->willReturn($first);
        $gameProphecy->uuid()->willReturn($second);
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
        $player0Prophecy->getUuid()->willReturn('0');
        $player1Prophecy->getUuid()->willReturn('1');

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
        $expectedContent = [];
        $history = new History();
        for ($i = 0; $i < History::LIMIT; $i++) {
            list($playerProphecy, $gameProphecy, $tileProphecy) = $this->setUpDependencies();
            $this->configureProphecies($playerProphecy, $gameProphecy, '0', '1');
            $value = new HistoryItem(
                $playerProphecy->reveal(),
                $tileProphecy->reveal(),
                $gameProphecy->reveal()
            );
            $expectedContent[$history->length($gameProphecy->reveal()) % History::LIMIT] = $value;
            $history->saveTurn(
                $playerProphecy->reveal(),
                $tileProphecy->reveal(),
                $gameProphecy->reveal()
            );
        }
        self::assertEquals(new HistoryContent($expectedContent), $history->content($gameProphecy->reveal()));
    }
}
