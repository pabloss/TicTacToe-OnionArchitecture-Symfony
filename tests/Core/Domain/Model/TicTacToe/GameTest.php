<?php
declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\TicTacToe;

use App\Core\Application\Command\TakeTileService;
use App\Core\Application\Service\TurnControl\ErrorLog;
use App\Core\Application\Service\TurnControl\PlayerRegistry;
use App\Core\Application\Service\TurnControl\TurnControl;
use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Board\Board;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Core\Application\Service\PlayersFactory;
use App\Tests\Stubs\History\History;
use PHPUnit\Framework\TestCase;

/**
 * Class GameTest
 * @package App\Tests\Core\Domain\Model\TicTacToe
 */
class GameTest extends TestCase
{
    /** @var Player[] */
    private $players;
    /** @var TicTacToe */
    private $game;
    /** @var TurnControl */
    private $turnControl;

    /** @var \App\Core\Application\Service\TurnControl\ErrorLog */
    private $errorLog;

    /**
     * @var History
     */
    private $history;

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
    public function game_should_record_correct_turns()
    {
        // When
        $history = new \App\Core\Application\Service\History\History();
        $takeTileService = new TakeTileService($this->game, $history, $this->turnControl);
        $symbols = [Symbol::PLAYER_X_SYMBOL, Symbol::PLAYER_0_SYMBOL];
        $expectedTileCoords = [[0, 0], [0, 1], [1, 0], [1, 1]];

        for ($i = 0; $i < \count($expectedTileCoords); ++$i){
            $row = $expectedTileCoords[$i][0];
            $column = $expectedTileCoords[$i][1];

            $takeTileService->takeTile($this->players[$symbols[$i%2]], new Tile($row,$column));
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
}
