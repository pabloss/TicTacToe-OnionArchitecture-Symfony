<?php
declare(strict_types=1);

namespace App\Tests\unit\Core\Domain\Model\TicTacToe\Game\Board;

use App\Core\Domain\Model\TicTacToe\Game\Board\Board;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use PHPUnit\Framework\TestCase;

/**
 * Class BoardTest
 * @package App\Tests\unit
 */
class BoardTest extends TestCase
{
    /**
     * @test
     */
    public function getTile()
    {
        // Given
        $playerXProphecy = $this->prophesize(Player::class);
        $tileProphecy = $this->prophesize(Tile::class);
        $tileProphecy->column()->willReturn(0);
        $tileProphecy->row()->willReturn(0);
        $board = new Board();

        // When
        $board->mark($tileProphecy->reveal(), $playerXProphecy->reveal());

        // Then
        self::assertSame($playerXProphecy->reveal(), $board->getPlayer($tileProphecy->reveal()));
    }
}
