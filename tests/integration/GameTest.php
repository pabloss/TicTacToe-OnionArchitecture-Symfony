<?php
declare(strict_types=1);

namespace App\Tests\integration;

use App\Core\Application\Command\TakeTileService;
use App\Core\Application\Errors\ErrorLog;
use App\Core\Application\Service\PlayerRegistry;
use App\Core\Application\Validation\TurnControl;
use App\Core\Domain\Model\TicTacToe\Game\Board\Board;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\History;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Core\Domain\Service\PlayersFactory;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    /** @var \App\Entity\Player[] */
    private $players;
    /** @var TicTacToe */
    private $game;
    /** @var TurnControl */
    private $turnControl;

    /** @var ErrorLog */
    private $errorLog;

    protected function setUp()
    {
        $playerRegistry = new PlayerRegistry();
        $playersFactory = new PlayersFactory();
        $this->players = $playersFactory->create();
        $this->game = new TicTacToe(new Board());
        $playerRegistry->registerPlayer(
            $this->players[Symbol::PLAYER_X_SYMBOL],
            $this->game
        );
        $playerRegistry->registerPlayer(
            $this->players[Symbol::PLAYER_0_SYMBOL],
            $this->game
        );
        $errorLog = new ErrorLog();
        $this->errorLog = $errorLog;

        $this->turnControl = new TurnControl($playerRegistry, $this->errorLog);
    }

    /**
     * @test
     */
    public function same_player_take_turn()
    {
        $game = new TicTacToe(new Board());
        $history = new History();
        $takeTileService = new TakeTileService($this->game, $history, $this->turnControl);
        $playerX = $this->players[Symbol::PLAYER_X_SYMBOL];

        $takeTileService->takeTile($playerX, new Tile(0, 0));
        $takeTileService->takeTile($playerX, new Tile(1, 1));
        self::assertEquals(
            ErrorLog::DUPLICATED_TURNS_ERROR,
            $this->errorLog->errors($game) & ErrorLog::DUPLICATED_TURNS_ERROR
        );
        self::assertTrue($this->errorLog->hasError(ErrorLog::DUPLICATED_TURNS_ERROR, $game));
    }

    /**
     * @test
     */
    public function game_could_not_allow_to_be_started_by_player0()
    {
        $game = new TicTacToe(new Board());
        $history = new History();
        $takeTileService = new TakeTileService($this->game, $history, $this->turnControl);
        $player0 = $this->players[Symbol::PLAYER_0_SYMBOL];
        $takeTileService->takeTile($player0, new Tile(0, 0));

        self::assertEquals(
            ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR,
            $this->errorLog->errors($game) & ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR
        );
        self::assertTrue($this->errorLog->hasError(ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR, $game));
    }
}
