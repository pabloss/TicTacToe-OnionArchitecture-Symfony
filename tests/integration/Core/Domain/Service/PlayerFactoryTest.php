<?php
declare(strict_types=1);

namespace App\Tests\integration\Core\Domain\Service;

use App\AppCore\DomainModel\Game\Player\Player;
use App\AppCore\DomainModel\Game\Player\Symbol;
use App\AppCore\DomainServices\PlayersFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class PlayerFactoryTest
 * @package App\Tests\integration
 */
class PlayerFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function createPlayers()
    {
        $factory = new PlayersFactory();
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $factory->create();
        self::assertInstanceOf(Player::class, $playerX);
        self::assertInstanceOf(Player::class, $player0);
    }

    /**
     * @test
     */
    public function factor_players()
    {
        $factory = new PlayersFactory();
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $factory->create();
        self::assertEquals('X', $playerX->symbol()->value());
        self::assertEquals('0', $player0->symbol()->value());
    }
}
