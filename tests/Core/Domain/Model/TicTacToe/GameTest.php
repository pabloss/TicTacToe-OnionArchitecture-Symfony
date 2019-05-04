<?php
declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\TicTacToe;

use App\Core\Application\Event\EventManager;
use App\Core\Application\Event\EventSubscriber\TakeTileEventSubscriber;
use App\Core\Domain\Event\EventInterface;
use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use App\Core\Domain\Service\FindWinner;
use App\Core\Domain\Service\PlayersFactory;
use App\Presentation\Web\Pub\Event\Event;
use App\Tests\Stubs\History\History;
use PHPUnit\Framework\TestCase;

/**
 * Class GameTest
 * @package App\Tests\Core\Domain\Model\TicTacToe
 */
class GameTest extends TestCase
{
    /** @var Game $game */
    private $game;

    /** @var Player $playerX */
    private $playerX;

    /** @var Player $player0 */
    private $player0;

    /**
     * @var History
     */
    private $history;

    /**
     * @return void
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
     */
    protected function setUp(): void
    {
        // Given
        $board = new Board();
        $history = new History();
        $findWinner = new FindWinner();
        $eventManger = new EventManager();
        TakeTileEventSubscriber::init($history);
        $eventManger->attach(
            Event::NAME,
            function (EventInterface $event) {
                return TakeTileEventSubscriber::onTakenTile($event);
            }
        );
        $uuid = uniqid();
        $factory = new PlayersFactory();
        $game = new TicTacToe($board, $history, $factory, $findWinner, $eventManger, $uuid);
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $game->players();

        $this->game = $game;
        $this->playerX = $playerX;
        $this->player0 = $player0;
        $this->history = $history;
    }

    /**
     * @test
     */
    public function game_should_record_correct_turns()
    {
        // When
        $players = [];
        $players[] = $this->playerX;
        $players[] = $this->player0;
        $expectedTileCoords = [[0, 0], [0, 1], [1, 0], [1, 1]];

        for ($i = 0; $i < \count($expectedTileCoords); ++$i){
            $row = $expectedTileCoords[$i][0];
            $column = $expectedTileCoords[$i][1];

            $players[$i%2]->takeTile(new Tile($row,$column), $this->game, $this->history);
        }

        // Then
        self::assertEquals([$this->playerX, $this->player0, null, $this->playerX, $this->player0, null, null, null, null], $this->game->board()->contents());
        self::assertEquals($expectedTileCoords,
            $this->history->content($this->game)->getTilesHistory()
        );
        self::assertEquals($this->game::OK, $this->game->errors());
    }

    /**
     * @test
     */
    public function game_should_not_produce_new_players_if_ones_already_exist()
    {
        // When
        list(Symbol::PLAYER_X_SYMBOL => $playerX1, Symbol::PLAYER_0_SYMBOL => $player01) = $this->game->players();
        list(Symbol::PLAYER_X_SYMBOL => $playerX2, Symbol::PLAYER_0_SYMBOL => $player02) = $this->game->players();

        // Then
        self::assertSame($playerX1, $playerX2);
        self::assertSame($player01, $player02);
    }
}
