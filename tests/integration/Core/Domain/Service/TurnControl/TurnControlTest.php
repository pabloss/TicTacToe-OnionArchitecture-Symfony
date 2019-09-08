<?php
declare(strict_types=1);

namespace App\Tests\integration\Core\Domain\Service\TurnControl;

use App\AppCore\DomainModel\Game\Board\TileInterface;
use App\AppCore\DomainModel\Game\GameInterface;
use App\AppCore\DomainModel\Game\Player\PlayerInterface;
use App\AppCore\DomainModel\Game\Player\Symbol;
use App\AppCore\DomainModel\History\HistoryContent;
use App\AppCore\DomainModel\History\HistoryInterface;
use App\AppCore\DomainServices\TurnControl\AccessControl;
use App\AppCore\DomainServices\TurnControl\ErrorLog;
use App\AppCore\DomainServices\TurnControl\ErrorLogInterface;
use App\AppCore\DomainServices\TurnControl\Params;
use App\AppCore\DomainServices\TurnControl\PlayerRegistry;
use App\AppCore\DomainServices\TurnControl\TurnControl;
use App\AppCore\DomainServices\TurnControl\Validation\GameShouldStartWithCorrectPlayerValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PlayerMustNotTakeTakenAlreadyTileValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PlayerShouldBeRegisteredValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PreviousPlayerShouldBeDifferentThanActualValidation;
use App\AppCore\DomainServices\TurnControl\Validation\ValidationCollection;
use PHPUnit\Framework\TestCase;

/**
 * Class TurnControlTest
 * @package App\Tests\integration
 */
class TurnControlTest extends TestCase
{
    /**
     * @test
     *
     * zapisz nowe wymagania do testu
     * a potem niech kod je spełni
     */
    public function validateTurn()
    {
        list($playerRegistry, $playerXProphecy, $playerOProphecy, $tileProphecy, $historyProphecy, $gameProphecy, $errorLogProphecy) = $this->configureGiven();

        $errorLogProphecy->addError(ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR, $gameProphecy->reveal())->shouldBeCalled();

        // When
        $playerRegistry->registerPlayer($playerXProphecy->reveal(), $gameProphecy->reveal());
        $playerRegistry->registerPlayer($playerOProphecy->reveal(), $gameProphecy->reveal());

        AccessControl::loadRegistry($playerRegistry);

        $turnControl = new TurnControl(new ValidationCollection(
            [
                ErrorLog::PLAYER_IS_NOT_ALLOWED => new PlayerShouldBeRegisteredValidation(),
                ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR => new GameShouldStartWithCorrectPlayerValidation(),
                ErrorLog::DUPLICATED_TURNS_ERROR => new PreviousPlayerShouldBeDifferentThanActualValidation(),
                ErrorLog::DUPLICATED_TILE_ERROR => new PlayerMustNotTakeTakenAlreadyTileValidation(),
            ]
        ), $errorLogProphecy->reveal());


        $turnControl->validateTurn(new Params($playerOProphecy->reveal(), $tileProphecy->reveal(), $gameProphecy->reveal(), $historyProphecy->reveal()));
    }

    /**
     * @return array
     */
    private function configureGiven(): array
    {
        $playerRegistry = new PlayerRegistry();

        $playerXProphecy = $this->prophesize(PlayerInterface::class);
        $playerXProphecy->symbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        $playerXProphecy->uuid()->willReturn('1');
        $playerOProphecy = $this->prophesize(PlayerInterface::class);
        $playerOProphecy->symbolValue()->willReturn(Symbol::PLAYER_0_SYMBOL);
        $playerOProphecy->uuid()->willReturn('2');

        $tileProphecy = $this->prophesize(TileInterface::class);

        $historyProphecy = $this->prophesize(HistoryInterface::class);
        $historyProphecy->getStartingPlayerSymbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        $gameProphecy = $this->prophesize(GameInterface::class);
        $gameProphecy->uuid()->willReturn(0);

        // influence in history flow - start
        $historyContentProphecy = $this->prophesize(HistoryContent::class);
        $historyContentProphecy->hasTile($tileProphecy->reveal())->willReturn(false);

        // simulation of staring game - start
        $historyProphecy->lastItem($gameProphecy->reveal())->willReturn(null);
        $historyProphecy->lastItemPlayerSymbolValue($gameProphecy->reveal())->willReturn(null);
        $historyProphecy->content($gameProphecy->reveal())->willReturn($historyContentProphecy->reveal());
        // simulation of staring game - end
        // influence in history flow - end

        $errorLogProphecy = $this->prophesize(ErrorLogInterface::class);
        return array(
            $playerRegistry,
            $playerXProphecy,
            $playerOProphecy,
            $tileProphecy,
            $historyProphecy,
            $gameProphecy,
            $errorLogProphecy
        );
    }

    /**
     * @test
     */
    public function notAllowedPlayer()
    {
        list($playerRegistry, $playerXProphecy, $playerOProphecy, $tileProphecy, $historyProphecy, $gameProphecy, $errorLogProphecy) = $this->configureGiven();
        $playerRegistry = new PlayerRegistry();
        $playerRegistry->registerPlayer($playerOProphecy->reveal(), $gameProphecy->reveal());
        AccessControl::loadRegistry($playerRegistry);

        $errorLogProphecy->addError(ErrorLog::PLAYER_IS_NOT_ALLOWED, $gameProphecy->reveal())->shouldBeCalled();

        // When
        $turnControl = new TurnControl(new ValidationCollection(
            [
                ErrorLog::PLAYER_IS_NOT_ALLOWED => new PlayerShouldBeRegisteredValidation(),
                ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR => new GameShouldStartWithCorrectPlayerValidation(),
                ErrorLog::DUPLICATED_TURNS_ERROR => new PreviousPlayerShouldBeDifferentThanActualValidation(),
                ErrorLog::DUPLICATED_TILE_ERROR => new PlayerMustNotTakeTakenAlreadyTileValidation(),
            ]
        ), $errorLogProphecy->reveal());

        $turnControl->validateTurn(new Params($playerXProphecy->reveal(), $tileProphecy->reveal(), $gameProphecy->reveal(), $historyProphecy->reveal()));
    }
}
