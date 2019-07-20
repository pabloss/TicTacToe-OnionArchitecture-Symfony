<?php
declare(strict_types=1);

namespace App\Tests\integration\Core\Domain\Model\TicTacToe\Game;

use App\Core\Application\Command\TakeTileService;
use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Board\Board;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Core\Domain\Service\PlayersFactory;
use App\Core\Domain\Service\TurnControl\ErrorLog;
use App\Core\Domain\Service\TurnControl\PlayerRegistry;
use App\Core\Domain\Service\TurnControl\TurnControl;
use App\Tests\Stubs\History\History;
use PHPUnit\Framework\TestCase;

/**
 * Class GameTest
 * @package App\Tests\integration\Core\Domain\Model\TicTacToe
 */
class GameTest extends TestCase
{
    /** @var Player[] */
    private $players;
    /** @var TicTacToe */
    private $game;
    /** @var TurnControl */
    private $turnControl;
    /** @var ErrorLog */
    private $errorLog;

    /**
     * @test
     */
    public function game_should_record_correct_turns()
    {
        // When
        $history = new \App\Core\Domain\Service\History\History();
        $takeTileService = new TakeTileService($this->game, $history, $this->turnControl);
        $symbols = [Symbol::PLAYER_X_SYMBOL, Symbol::PLAYER_0_SYMBOL];
        $expectedTileCoords = [[0, 0], [0, 1], [1, 0], [1, 1]];

        for ($i = 0; $i < count($expectedTileCoords); ++$i) {
            $row = $expectedTileCoords[$i][0];
            $column = $expectedTileCoords[$i][1];

            $takeTileService->takeTile($this->players[$symbols[$i % 2]], new Tile($row, $column));
        }

        // Then
        self::assertEquals(
            [
                $this->players[Symbol::PLAYER_X_SYMBOL],
                $this->players[Symbol::PLAYER_0_SYMBOL],
                null,
                $this->players[Symbol::PLAYER_X_SYMBOL],
                $this->players[Symbol::PLAYER_0_SYMBOL],
                null,
                null,
                null,
                null
            ], $this->game->board()->contents());
        self::assertEquals($expectedTileCoords,
            $history->content($this->game)->getTilesHistory()
        );
        self::assertEquals(ErrorLog::OK, $this->errorLog->errors($this->game));
    }

    /**
     * @test
     */
    public function same_player_take_turn()
    {
        $this->game = new TicTacToe(new Board(), \uniqid());
        $history = new \App\Core\Domain\Service\History\History();
        $takeTileService = new TakeTileService($this->game, $history, $this->turnControl);
        $playerX = $this->players[Symbol::PLAYER_X_SYMBOL];

        $takeTileService->takeTile($playerX, new Tile(0, 0));
        $takeTileService->takeTile($playerX, new Tile(1, 1));
        self::assertEquals(
            ErrorLog::DUPLICATED_TURNS_ERROR,
            $this->errorLog->errors($this->game) & ErrorLog::DUPLICATED_TURNS_ERROR
        );
        self::assertTrue($this->errorLog->hasError(ErrorLog::DUPLICATED_TURNS_ERROR, $this->game));
    }

    /**
     * @test
     */
    public function game_could_not_allow_to_be_started_by_player0()
    {
        $this->game = new TicTacToe(new Board(), \uniqid());
        $history = new History();
        $takeTileService = new TakeTileService($this->game, $history, $this->turnControl);
        $player0 = $this->players[Symbol::PLAYER_0_SYMBOL];
        $takeTileService->takeTile($player0, new Tile(0, 0));

        self::assertEquals(
            ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR,
            $this->errorLog->errors($this->game) & ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR
        );
        self::assertTrue($this->errorLog->hasError(ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR, $this->game));
    }

    /**
     * @return void
     * @throws NotAllowedSymbolValue
     */
    protected function setUp(): void
    {
        // Given
        $playerRegistry = new PlayerRegistry();
        $playersFactory = new PlayersFactory();
        $this->players = $playersFactory->create();
        $this->game = new TicTacToe(new Board(), \uniqid());
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
}
