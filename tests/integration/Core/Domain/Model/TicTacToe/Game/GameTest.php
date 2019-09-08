<?php
declare(strict_types=1);

namespace App\Tests\integration\Core\Domain\Model\TicTacToe\Game;

use App\AppCore\DomainModel\Game\Board\Board;
use App\AppCore\DomainModel\Game\Board\Tile;
use App\AppCore\DomainModel\Game\Exception\NotAllowedSymbolValue;
use App\AppCore\DomainModel\Game\Game as TicTacToe;
use App\AppCore\DomainModel\Game\Player\Player;
use App\AppCore\DomainModel\Game\Player\Symbol;
use App\AppCore\DomainServices\PlayersFactory;
use App\AppCore\DomainServices\TakeTileService;
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
        $history = new History();
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
        $history = new History();
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
        AccessControl::loadRegistry($playerRegistry);
        $errorLog = new ErrorLog();
        $this->errorLog = $errorLog;

        $this->turnControl = new TurnControl(new ValidationCollection(
            [
                ErrorLog::PLAYER_IS_NOT_ALLOWED => new PlayerShouldBeRegisteredValidation(),
                ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR => new GameShouldStartWithCorrectPlayerValidation(),
                ErrorLog::DUPLICATED_TURNS_ERROR => new PreviousPlayerShouldBeDifferentThanActualValidation(),
                ErrorLog::DUPLICATED_TILE_ERROR => new PlayerMustNotTakeTakenAlreadyTileValidation(),
            ]
        ), $this->errorLog);
    }
}
