<?php
declare(strict_types=1);

namespace App\Tests\integration;

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

    //todo: dorób błędy dla niezarejstrowancyh playerów
    /**
     * @test
     */
    public function takeTile()
    {
        $playerX = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), \uniqid());
        $playerO = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), \uniqid());
        $game = new Game(new Board());
        $playerRegistry = new PlayerRegistry();
        $playerRegistry->registerPlayer($playerX, $game);
        $playerRegistry->registerPlayer($playerO, $game);
        $turnControl = new TurnControl($playerRegistry);
        $service = new TakeTileService($game, new History(), $turnControl);

        $tile = new Tile(0, 0);
        $service->takeTile($playerX, $tile);
        self::assertEquals($playerX, $game->board()->getPlayer($tile));
    }

    /**
     * @test
     */
    public function takeTileByWrongPlayer()
    {
        $playerO = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), \uniqid());
        $playerX = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), \uniqid());
        $game = new Game(new Board());
        $playerRegistry = new PlayerRegistry();
        $playerRegistry->registerPlayer($playerX, $game);
        $playerRegistry->registerPlayer($playerO, $game);
        $turnControl = new TurnControl($playerRegistry);
        $service = new TakeTileService($game, new History(), $turnControl);

        $tile = new Tile(0, 0);
        $service->takeTile($playerO, $tile);
        self::assertTrue($service->hasError(Game::GAME_STARTED_BY_PLAYER0_ERROR));
    }

    /**
     * @test
     */
    public function takeTileByNonRegisteredPlayer()
    {
        $playerO = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), \uniqid());
        $game = new Game(new Board());
        $playerRegistry = new PlayerRegistry();
        $turnControl = new TurnControl($playerRegistry);
        $service = new TakeTileService($game, new History(), $turnControl);

        $tile = new Tile(0, 0);
        $service->takeTile($playerO, $tile);
        self::assertTrue($service->hasError(Game::PLAYER_IS_NOT_ALLOWED));
        self::assertTrue($service->hasError(Game::GAME_STARTED_BY_PLAYER0_ERROR));
    }

    /**
     * @test
     */
    public function takeTileByDuplacatedMove()
    {
        $playerO = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), \uniqid());
        $game = new Game(new Board());
        $playerRegistry = new PlayerRegistry();
        $turnControl = new TurnControl($playerRegistry);
        $playerRegistry->registerPlayer($playerO, $game);
        $service = new TakeTileService($game, new History(), $turnControl);

        $tile = new Tile(0, 0);
        $service->takeTile($playerO, $tile);
        $service->takeTile($playerO, $tile);
        self::assertTrue($service->hasError(Game::DUPLICATED_TURNS_ERROR));
    }
}
