<?php
declare(strict_types=1);

namespace App\Tests\unit\Core\Domain\Model\TicTacToe\Game;

use App\AppCore\DomainModel\Game\Board\Board;
use App\AppCore\DomainModel\Game\Game;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    /**
     * @test
     */
    public function uuid()
    {
        // Given
        $uuid = \uniqid();
        $boardProphecy = $this->prophesize(Board::class);

        // When
        $game = new Game($boardProphecy->reveal(), $uuid);

        // Then
        self::assertEquals($uuid, $game->uuid());
    }
}
