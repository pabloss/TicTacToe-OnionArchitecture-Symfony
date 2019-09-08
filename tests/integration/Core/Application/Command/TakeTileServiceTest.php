<?php
declare(strict_types=1);

namespace App\Tests\integration\Core\Application\Command;

use App\AppCore\ApplicationServices\TakeTileService;
use App\AppCore\DomainModel\Game\Board\Board;
use App\AppCore\DomainModel\Game\Board\Tile;
use App\AppCore\DomainModel\Game\Game;
use App\AppCore\DomainModel\Game\Player\Player;
use App\AppCore\DomainModel\Game\Player\Symbol;
use App\AppCore\DomainServices\TurnControl\AccessControl;
use App\AppCore\DomainServices\TurnControl\ErrorLog;
use App\AppCore\DomainServices\TurnControl\PlayerRegistry;
use App\AppCore\DomainServices\TurnControl\TurnControl;
use App\AppCore\DomainServices\TurnControl\Validation\GameShouldStartWithCorrectPlayerValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PlayerMustNotTakeTakenAlreadyTileValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PlayerShouldBeRegisteredValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PreviousPlayerShouldBeDifferentThanActualValidation;
use App\AppCore\DomainServices\TurnControl\Validation\ValidationCollection;
use App\Tests\Stubs\History\History;
use PHPUnit\Framework\TestCase;

class TakeTileServiceTest extends TestCase
{

    /**
     * @test
     */
    public function takeTile()
    {
        $errorLog = new ErrorLog();

        $playerX = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), uniqid());
        $playerO = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), uniqid());
        $game = new Game(new Board(), \uniqid());
        $playerRegistry = new PlayerRegistry();
        $playerRegistry->registerPlayer($playerX, $game);
        $playerRegistry->registerPlayer($playerO, $game);
        AccessControl::loadRegistry($playerRegistry);
        $turnControl = new TurnControl(new ValidationCollection(
            [
                ErrorLog::PLAYER_IS_NOT_ALLOWED => new PlayerShouldBeRegisteredValidation(),
                ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR => new GameShouldStartWithCorrectPlayerValidation(),
                ErrorLog::DUPLICATED_TURNS_ERROR => new PreviousPlayerShouldBeDifferentThanActualValidation(),
                ErrorLog::DUPLICATED_TILE_ERROR => new PlayerMustNotTakeTakenAlreadyTileValidation(),
            ]
        ), $errorLog);
        $service = new TakeTileService($game, new History(), $turnControl);

        $tile = new Tile(0, 0);
        $service->takeTile($playerX, $tile);
        self::assertEquals($playerX, $game->board()->getPlayer($tile));
        self::assertTrue($errorLog->noErrors($game));
    }

    /**
     * @test
     */
    public function takeTileByWrongPlayer()
    {
        $errorLog = new ErrorLog();
        $playerO = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), uniqid());
        $playerX = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), uniqid());
        $game = new Game(new Board(), \uniqid());
        $playerRegistry = new PlayerRegistry();
        $playerRegistry->registerPlayer($playerX, $game);
        $playerRegistry->registerPlayer($playerO, $game);
        $turnControl = new TurnControl(new ValidationCollection(
            [
                ErrorLog::PLAYER_IS_NOT_ALLOWED => new PlayerShouldBeRegisteredValidation(),
                ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR => new GameShouldStartWithCorrectPlayerValidation(),
                ErrorLog::DUPLICATED_TURNS_ERROR => new PreviousPlayerShouldBeDifferentThanActualValidation(),
                ErrorLog::DUPLICATED_TILE_ERROR => new PlayerMustNotTakeTakenAlreadyTileValidation(),
            ]
        ), $errorLog);
        $service = new \App\AppCore\ApplicationServices\TakeTileService($game, new History(), $turnControl);

        $tile = new Tile(0, 0);
        $service->takeTile($playerO, $tile);
        self::assertFalse($errorLog->noErrors($game));
        self::assertTrue($errorLog->hasError(ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR, $game));
    }

    /**
     * @test
     */
    public function takeTileByNonRegisteredPlayer()
    {
        $errorLog = new ErrorLog();
        $playerO = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), uniqid());
        $game = new Game(new Board(), \uniqid());
        $playerRegistry = new PlayerRegistry();
        $turnControl = new TurnControl(new ValidationCollection(
            [
                ErrorLog::PLAYER_IS_NOT_ALLOWED => new PlayerShouldBeRegisteredValidation(),
                ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR => new GameShouldStartWithCorrectPlayerValidation(),
                ErrorLog::DUPLICATED_TURNS_ERROR => new PreviousPlayerShouldBeDifferentThanActualValidation(),
                ErrorLog::DUPLICATED_TILE_ERROR => new PlayerMustNotTakeTakenAlreadyTileValidation(),
            ]
        ), $errorLog);
        $service = new \App\AppCore\ApplicationServices\TakeTileService($game, new History(), $turnControl);

        $tile = new Tile(0, 0);
        $service->takeTile($playerO, $tile);
        self::assertFalse($errorLog->noErrors($game));
        self::assertTrue($errorLog->hasError(ErrorLog::PLAYER_IS_NOT_ALLOWED, $game));
        self::assertTrue($errorLog->hasError(ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR, $game));
    }

    /**
     * @test
     */
    public function takeTileByDuplacatedMove()
    {
        $errorLog = new ErrorLog();
        $playerX = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), uniqid());
        $game = new Game(new Board(), \uniqid());
        $playerRegistry = new PlayerRegistry();
        $turnControl = new TurnControl(new ValidationCollection(
            [
                ErrorLog::PLAYER_IS_NOT_ALLOWED => new PlayerShouldBeRegisteredValidation(),
                ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR => new GameShouldStartWithCorrectPlayerValidation(),
                ErrorLog::DUPLICATED_TURNS_ERROR => new PreviousPlayerShouldBeDifferentThanActualValidation(),
                ErrorLog::DUPLICATED_TILE_ERROR => new PlayerMustNotTakeTakenAlreadyTileValidation(),
            ]
        ), $errorLog);
        $playerRegistry->registerPlayer($playerX, $game);
        $service = new \App\AppCore\ApplicationServices\TakeTileService($game, new History(), $turnControl);

        $tile = new Tile(0, 0);
        $service->takeTile($playerX, $tile);
        $service->takeTile($playerX, $tile);
        self::assertFalse($errorLog->noErrors($game));
        self::assertTrue($errorLog->hasError(ErrorLog::DUPLICATED_TURNS_ERROR, $game));
    }
}
