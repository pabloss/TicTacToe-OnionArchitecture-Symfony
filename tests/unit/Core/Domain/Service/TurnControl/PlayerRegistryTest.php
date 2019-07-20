<?php
declare(strict_types=1);

namespace App\Tests\unit\Core\Domain\Service\TurnControl;

use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Core\Domain\Service\TurnControl\PlayerRegistry;
use PHPUnit\Framework\TestCase;

/**
 * Class PlayerRegistryTest
 * @package App\Tests\unit\Core\Domain\Service
 */
class PlayerRegistryTest extends TestCase
{
    /**
     * @test
     */
    public function playersShouldBeRegistered()
    {
        // Given
        // create Player X
        $playerXProphecy = $this->prophesize(Player::class);
        $playerXProphecy->uuid()->willReturn(0);
        $playerXProphecy->symbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        // create Player O
        $playerOProphecy = $this->prophesize(Player::class);
        $playerOProphecy->uuid()->willReturn(1);
        $playerOProphecy->symbolValue()->willReturn(Symbol::PLAYER_0_SYMBOL);
        // create game
        $gameProphecy = $this->prophesize(Game::class);
        $gameProphecy->uuid()->willReturn(2);

        // When
        $playerRegistryService = new PlayerRegistry();

        // register both
        $playerRegistryService->registerPlayer(
            $playerXProphecy->reveal(),
            $gameProphecy->reveal()
        );
        $playerRegistryService->registerPlayer(
            $playerOProphecy->reveal(),
            $gameProphecy->reveal()
        );

        // Then
        // take them
        // they are the same for game

        self::assertSame($playerXProphecy->reveal()->uuid(),
            $playerRegistryService->players($gameProphecy->reveal())[Symbol::PLAYER_X_SYMBOL]);
        self::assertSame($playerOProphecy->reveal()->uuid(),
            $playerRegistryService->players($gameProphecy->reveal())[Symbol::PLAYER_0_SYMBOL]);
    }
}
