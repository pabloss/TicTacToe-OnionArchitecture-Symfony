<?php
declare(strict_types=1);

namespace App\Tests\unit;

use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
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
        $playerX = $this->prophesize(Player::class);
        $tile = $this->prophesize(Tile::class);
        $tile->column()->willReturn(0);
        $tile->row()->willReturn(0);
        $board = new Board();
        $board->mark($tile->reveal(), $playerX->reveal());
        self::assertSame($playerX->reveal(), $board->getPlayer($tile->reveal()));
    }
}
