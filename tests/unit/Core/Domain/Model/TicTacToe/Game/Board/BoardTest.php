<?php
declare(strict_types=1);

namespace App\Tests\unit\Core\Domain\Model\TicTacToe\Game\Board;

use App\AppCore\DomainModel\Game\Board\Board;
use App\AppCore\DomainModel\Game\Player\Player;
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
        $tileProphecy = $this->prophesize(\App\AppCore\DomainModel\Game\Board\Tile::class);
        $tileProphecy->column()->willReturn(0);
        $tileProphecy->row()->willReturn(0);
        $board = new Board();

        // When
        $board->mark($tileProphecy->reveal(), $playerXProphecy->reveal());

        // Then
        self::assertSame($playerXProphecy->reveal(), $board->getPlayer($tileProphecy->reveal()));
    }
}
