<?php
declare(strict_types=1);

namespace App\Tests\integration;

use App\Core\Application\Errors\ErrorLog;
use App\Core\Application\Service\PlayerRegistry;
use App\Core\Application\Validation\TurnControl;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use PHPUnit\Framework\TestCase;

/**
 * Class TurnControlTest
 * @package App\Tests\integration
 */
class TurnControlTest extends TestCase
{
    /**
     * @test
     */
    public function validateTurn()
    {
        list($playerRegistry, $playerXProphecy, $playerOProphecy, $historyProphecy, $gameProphecy, $errorLogProphecy) = $this->configureGiven();

        $errorLogProphecy->addError(ErrorLog::PLAYER_IS_NOT_ALLOWED, $gameProphecy->reveal())->shouldNotBeCalled();

        // When
        $playerRegistry->registerPlayer($playerXProphecy->reveal(), $gameProphecy->reveal());
        $playerRegistry->registerPlayer($playerOProphecy->reveal(), $gameProphecy->reveal());
        $turnControl = new TurnControl($playerRegistry, $errorLogProphecy->reveal());

        $turnControl->validateTurn($playerXProphecy->reveal(), $gameProphecy->reveal(), $historyProphecy->reveal());
    }

    /**
     * @test
     */
    public function notAllowedPlayer()
    {
        list($playerRegistry, $playerXProphecy, $playerOProphecy, $historyProphecy, $gameProphecy, $errorLogProphecy) = $this->configureGiven();

        $errorLogProphecy->addError(ErrorLog::PLAYER_IS_NOT_ALLOWED, $gameProphecy->reveal())->shouldBeCalled();

        // When
        $turnControl = new TurnControl($playerRegistry, $errorLogProphecy->reveal());

        $turnControl->validateTurn($playerXProphecy->reveal(), $gameProphecy->reveal(), $historyProphecy->reveal());
    }

    /**
     * @return array
     */
    private function configureGiven(): array
    {
        $playerRegistry = new PlayerRegistry();

        $playerXProphecy = $this->prophesize(Player::class);
        $playerXProphecy->symbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        $playerXProphecy->uuid()->willReturn(1);
        $playerOProphecy = $this->prophesize(Player::class);
        $playerOProphecy->symbolValue()->willReturn(Symbol::PLAYER_0_SYMBOL);
        $playerOProphecy->uuid()->willReturn(2);

        $historyProphecy = $this->prophesize(HistoryInterface::class);
        $historyProphecy->getStartingPlayerSymbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        $gameProphecy = $this->prophesize(Game::class);
        $gameProphecy->uuid()->willReturn(0);
        $historyProphecy->lastItem($gameProphecy->reveal())->willReturn(null);
        $errorLogProphecy = $this->prophesize(ErrorLog::class);
        return array($playerRegistry, $playerXProphecy, $playerOProphecy, $historyProphecy, $gameProphecy, $errorLogProphecy);
    }
}
