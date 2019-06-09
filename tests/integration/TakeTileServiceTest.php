<?php
declare(strict_types=1);

namespace App\Tests\integration;

use App\Core\Application\Service\TakeTileService;
use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use PHPUnit\Framework\TestCase;

class TakeTileServiceTest extends TestCase
{

    //todo: dorób błędy dla niezarejstrowancyh playerów
    /**
     * @test
     */
    public function takeTile()
    {
        $playerO = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), \uniqid());
        $playerX = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), \uniqid());
        $game = new Game(new Board());
        $service = new TakeTileService($game);

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
        $service = new TakeTileService($game);

        $tile = new Tile(0, 0);
        $service->takeTile($playerO, $tile);
        self::assertTrue($service->hasError(Game::GAME_STARTED_BY_PLAYER0_ERROR));
    }
}
