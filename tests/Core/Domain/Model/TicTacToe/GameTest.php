<?php
declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\TicTacToe;

use App\Core\Application\Event\EventSubscriber\TakeTileEventSubscriber;
use App\Core\Domain\Event\EventInterface;
use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use App\Core\Domain\Service\FindWinner;
use App\Core\Domain\Service\PlayersFactory;
use App\Presentation\Web\Pub\Event\Event;
use App\Tests\Stubs\Event\EventManager;
use App\Tests\Stubs\History\History;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{

    /**
     * @test
     */
    public function game_should_record_correct_turns()
    {
        $board = new Board();
        $history = new History();
        $findWinner = new FindWinner();
        $eventManger = new EventManager();
        $eventManger->attach(
            Event::NAME,
            function (EventInterface $event) {
                return TakeTileEventSubscriber::onTakenTile($event);
            }
        );
        $uuid = uniqid();
        $factory = new PlayersFactory($eventManger);
        $game = new TicTacToe($board, $history, $factory, $findWinner, $eventManger, $uuid);
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $game->players();
        $playerX->takeTile(new Tile(0, 0), $game);
        $player0->takeTile(new Tile(0, 1), $game);
        $playerX->takeTile(new Tile(1, 0), $game);
        $player0->takeTile(new Tile(1, 1), $game);
        self::assertEquals([$playerX, $player0, null, $playerX, $player0, null, null, null, null], $game->board()->contents());
        $historyTiles = [];
        $historyItems = $game->history()->content($game);
        /** @var HistoryItem $historyItem */
        foreach ($historyItems as $historyItem) {
            $tile = $historyItem->tile();
            $historyTiles[] = [$tile->row(), $tile->column()];
        }
        self::assertEquals([[0, 0], [0, 1], [1, 0], [1, 1]], $historyTiles);
        self::assertEquals($game::OK, $game->errors());
    }

    /**
     * @test
     */
    public function game_should_not_produce_new_players_if_ones_already_exist()
    {
        $board = new Board();
        $history = new History();
        $findWinner = new FindWinner();
        $eventManger = new EventManager();
        $eventManger->attach(
            Event::class,
            function (EventInterface $event) {
                return TakeTileEventSubscriber::onTakenTile($event);
            }
        );
        $uuid = uniqid();
        $factory = new PlayersFactory($eventManger);
        $game = new TicTacToe($board, $history, $factory, $findWinner, $eventManger, $uuid);
        list(Symbol::PLAYER_X_SYMBOL => $playerX1, Symbol::PLAYER_0_SYMBOL => $player01) = $game->players();
        list(Symbol::PLAYER_X_SYMBOL => $playerX2, Symbol::PLAYER_0_SYMBOL => $player02) = $game->players();

        self::assertSame($playerX1, $playerX2);
        self::assertSame($player01, $player02);
    }
}
