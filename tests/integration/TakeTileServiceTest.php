<?php
declare(strict_types=1);

namespace App\Tests\integration;

use App\Core\Application\Errors\ErrorLog;
use App\Core\Application\Service\PlayerRegistry;
use App\Core\Application\Service\TakeTileService;
use App\Core\Application\Validation\TurnControl;
use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
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

        $playerX = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), \uniqid());
        $playerO = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), \uniqid());
        $game = new Game(new Board());
        $playerRegistry = new PlayerRegistry();
        $playerRegistry->registerPlayer($playerX, $game);
        $playerRegistry->registerPlayer($playerO, $game);
        $turnControl = new TurnControl($playerRegistry, $errorLog);
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
        $playerO = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), \uniqid());
        $playerX = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), \uniqid());
        $game = new Game(new Board());
        $playerRegistry = new PlayerRegistry();
        $playerRegistry->registerPlayer($playerX, $game);
        $playerRegistry->registerPlayer($playerO, $game);
        $turnControl = new TurnControl($playerRegistry, $errorLog);
        $service = new TakeTileService($game, new History(), $turnControl);

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
        $playerO = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), \uniqid());
        $game = new Game(new Board());
        $playerRegistry = new PlayerRegistry();
        $turnControl = new TurnControl($playerRegistry, $errorLog);
        $service = new TakeTileService($game, new History(), $turnControl);

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
        $playerO = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), \uniqid());
        $game = new Game(new Board());
        $playerRegistry = new PlayerRegistry();
        $turnControl = new TurnControl($playerRegistry, $errorLog);
        $playerRegistry->registerPlayer($playerO, $game);
        $service = new TakeTileService($game, new History(), $turnControl);

        $tile = new Tile(0, 0);
        $service->takeTile($playerO, $tile);
        $service->takeTile($playerO, $tile);
        self::assertFalse($errorLog->noErrors($game));
        self::assertTrue($errorLog->hasError(ErrorLog::DUPLICATED_TURNS_ERROR, $game));
    }
}
