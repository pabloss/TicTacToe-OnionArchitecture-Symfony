<?php
declare(strict_types=1);

namespace App\Tests\unit\Core\Domain\Service\TurnControl;

use App\AppCore\DomainModel\Game\GameInterface;
use App\AppCore\DomainModel\Game\Player\PlayerInterface;
use App\AppCore\DomainModel\Game\Player\Symbol;
use App\AppCore\DomainServices\TurnControl\PlayerRegistry;
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
        $playerXProphecy = $this->prophesize(PlayerInterface::class);
        $playerXProphecy->uuid()->willReturn(0);
        $playerXProphecy->symbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        // create Player O
        $playerOProphecy = $this->prophesize(PlayerInterface::class);
        $playerOProphecy->uuid()->willReturn(1);
        $playerOProphecy->symbolValue()->willReturn(Symbol::PLAYER_0_SYMBOL);
        // create game
        $gameProphecy = $this->prophesize(GameInterface::class);
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
