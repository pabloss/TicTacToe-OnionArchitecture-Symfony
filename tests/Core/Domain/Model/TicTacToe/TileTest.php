<?php
declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\TicTacToe;

use PHPUnit\Framework\TestCase;

class TileTest extends TestCase
{
    /**
     * @test
     */
    public function gets_row_and_column()
    {
        $tile = new \App\Core\Domain\Model\TicTacToe\ValueObject\Tile(1, 2);
        self::assertEquals(1, $tile->row());
        self::assertEquals(2, $tile->column());
    }

    /**
     * @test
     * @expectedException App\Core\Domain\Model\TicTacToe\Exception\OutOfLegalSizeException
     */
    public function throws_exceptions_on_illegal_position__column()
    {
        new \App\Core\Domain\Model\TicTacToe\ValueObject\Tile(1, 3);
    }

    /**
     * @test
     * @expectedException App\Core\Domain\Model\TicTacToe\Exception\OutOfLegalSizeException
     */
    public function throws_exceptions_on_illegal_position__row()
    {
        new \App\Core\Domain\Model\TicTacToe\ValueObject\Tile(3, 1);
    }

    /**
     * @test
     * @expectedException App\Core\Domain\Model\TicTacToe\Exception\OutOfLegalSizeException
     */
    public function throws_exceptions_on_illegal_position__both()
    {
        new \App\Core\Domain\Model\TicTacToe\ValueObject\Tile(3, 5);
    }
}
