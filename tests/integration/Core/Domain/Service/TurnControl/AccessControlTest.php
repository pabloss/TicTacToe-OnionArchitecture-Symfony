<?php
declare(strict_types=1);

namespace App\Tests\integration\Core\Domain\Service\TurnControl;

use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Core\Domain\Service\TurnControl\AccessControl;
use App\Core\Domain\Service\TurnControl\PlayerRegistry;
use PHPUnit\Framework\TestCase;

/**
 * Class AccessControlTest
 * @package App\Tests\integration
 */
class AccessControlTest extends TestCase
{
    /**
     * @test
     */
    public function isPlayerAllowed()
    {
        // Given
        // using player registry we should keep access control feature
        // registry should have only one instance
        $registry = new PlayerRegistry();
        // when registering player X
        // and when registering player 9
        // for game
        // we should be allowed to access game
        $gameProphecy = $this->prophesize(Game::class);
        $gameProphecy->uuid()->willReturn(0);
        $playerXProphecy = $this->prophesize(Player::class);
        $playerXProphecy->symbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        $playerXProphecy->uuid()->willReturn(1);
        $playerOProphecy = $this->prophesize(Player::class);
        $playerOProphecy->symbolValue()->willReturn(Symbol::PLAYER_0_SYMBOL);
        $playerOProphecy->uuid()->willReturn(2);

        // When
        $registry->registerPlayer($playerXProphecy->reveal(), $gameProphecy->reveal());
        $registry->registerPlayer($playerOProphecy->reveal(), $gameProphecy->reveal());
        AccessControl::loadRegistry($registry);

        // Then
        self::assertTrue(AccessControl::isPlayerAllowed($playerXProphecy->reveal(), $gameProphecy->reveal()));
        self::assertTrue(AccessControl::isPlayerAllowed($playerOProphecy->reveal(), $gameProphecy->reveal()));
    }
}
