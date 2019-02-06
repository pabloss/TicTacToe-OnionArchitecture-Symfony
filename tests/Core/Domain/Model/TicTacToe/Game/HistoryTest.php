<?php
declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\TicTacToe;

use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player;
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
        list($playerProphecy, $gameProphecy, $tileProphecy) = $this->setUpProphecies();
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
    private function setUpProphecies(): array
    {
        $playerProphecy = $this->prophesize(Player::class);
        $gameProphecy = $this->prophesize(Game::class);
        $tileProphecy = $this->prophesize(Tile::class);

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
        $history = new History();
        list($playerProphecy, $gameProphecy, $tileProphecy) = $this->setUpProphecies();
        $this->configureProphecies($playerProphecy, $gameProphecy, '0', '0');
        $history->saveTurn(
            $playerProphecy->reveal(),
            $tileProphecy->reveal(),
            $gameProphecy->reveal()
        );
        self::assertEquals('0', $history->getLastTurn($gameProphecy->reveal())->player()->getUuid());

        list($playerProphecy, $gameProphecy, $tileProphecy) = $this->setUpProphecies();
        $this->configureProphecies($playerProphecy, $gameProphecy, '1', '0');
        $history->saveTurn(
            $playerProphecy->reveal(),
            $tileProphecy->reveal(),
            $gameProphecy->reveal()
        );
        self::assertEquals('1', $history->getLastTurn($gameProphecy->reveal())->player()->getUuid());

        self::assertEquals(2, $history->length($gameProphecy->reveal()));

        for ($i = 0; $i < ($history::LIMIT * 2); $i++) {
            list($playerProphecy, $gameProphecy, $tileProphecy) = $this->setUpProphecies();
            $this->configureProphecies($playerProphecy, $gameProphecy, '0', '1');
            $history->saveTurn(
                $playerProphecy->reveal(),
                $tileProphecy->reveal(),
                $gameProphecy->reveal()
            );
        }
        self::assertEquals($history::LIMIT, $history->length($gameProphecy->reveal()));
    }

    /**
     * @test
     */
    public function history_should_return_contents()
    {
        $expectedContent = [];
        $history = new History();
        for ($i = 0; $i < History::LIMIT; $i++) {
            list($playerProphecy, $gameProphecy, $tileProphecy) = $this->setUpProphecies();
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
        self::assertEquals($expectedContent, $history->content($gameProphecy->reveal()));
    }
}
