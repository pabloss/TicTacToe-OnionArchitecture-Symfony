<?php
declare(strict_types=1);

namespace App\Tests\integration;

use App\Core\Application\Event\EventManager;
use App\Core\Application\Event\EventSubscriber\TakeTileEventSubscriber;
use App\Core\Domain\Event\EventInterface;
use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\History;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use App\Core\Domain\Service\FindWinner;
use App\Core\Domain\Service\PlayersFactory;
use App\Presentation\Web\Pub\Event\Event;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicGameplayTest extends WebTestCase
{

    /**
     * @test
     *
     * End result:
     * 0X0
     * -X-
     * -X-
     *
     * Hint: best way of solving a problem is making sure that the problem does
     * not exist in the first place
     */
    public function complete_happy_path_gameplay()
    {
        $history = new History();
        TakeTileEventSubscriber::init($history);
        $eventManager = EventManager::getInstance();
        $eventManager->attach(Event::NAME, function (EventInterface $event){
            TakeTileEventSubscriber::onTakenTile($event);
        });
        $game = new TicTacToe(new Board(), $history, new PlayersFactory(), new FindWinner(),
            EventManager::getInstance(),
            \uniqid()
        );
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $game->players();
        $playerX->takeTile(new Tile(1, 1), $game, $history);
        $player0->takeTile(new Tile(0, 0), $game, $history);
        $playerX->takeTile(new Tile(0, 1), $game, $history);
        $player0->takeTile(new Tile(0, 2), $game, $history);
        $playerX->takeTile(new Tile(2, 1), $game, $history);
        $this->assertSame($playerX, $game->winner());
    }

    /**
     * @test
     */
    public function complete_happy_path_gameplay_other_player_wins()
    {
        // We are swapping players
        $history = new History();
        TakeTileEventSubscriber::init($history);
        $eventManager = EventManager::getInstance();
        $eventManager->detach(Event::NAME);
        $eventManager->attach(Event::NAME, function (EventInterface $event){
            TakeTileEventSubscriber::onTakenTile($event);
        });
        $game = new TicTacToe(new Board(), $history, new PlayersFactory(), new FindWinner(),
            $eventManager,
            \uniqid()
        );
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $game->players();
        $playerX->takeTile(new Tile(2, 2), $game, $history);
        $player0->takeTile(new Tile(1, 1), $game, $history);
        $playerX->takeTile(new Tile(0, 0), $game, $history);
        $player0->takeTile(new Tile(0, 1), $game, $history);
        $playerX->takeTile(new Tile(0, 2), $game, $history);
        $player0->takeTile(new Tile(2, 1), $game, $history);
        $this->assertSame($player0, $game->winner());
    }

    /**
     * @test
     *
     * End result:
     * 0X0
     * -X-
     * -X-
     *
     * Hint: best way of solving a problem is making sure that the problem does
     * not exist in the first place
     */
    public function complete_happy_path_gameplay_use_another_implementation()
    {
        $client = self::createClient();

        $history = new History();
        TakeTileEventSubscriber::init($history);
        $game = new TicTacToe(new Board(), $history, new PlayersFactory(), new FindWinner(),
            $client->getContainer()->get(\App\Tests\Stubs\Event\Framework\EventManager::class),
            \uniqid()
        );
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $game->players();
        $playerX->takeTile(new Tile(1, 1), $game, $history);
        $player0->takeTile(new Tile(0, 0), $game, $history);
        $playerX->takeTile(new Tile(0, 1), $game, $history);
        $player0->takeTile(new Tile(0, 2), $game, $history);
        $playerX->takeTile(new Tile(2, 1), $game, $history);
        $this->assertSame($playerX, $game->winner());
    }

    /**
     * @test
     */
    public function complete_happy_path_gameplay_other_player_wins_use_another_implementation()
    {
        $client = self::createClient();

        // We are swapping players
        $history = new History();
        TakeTileEventSubscriber::init($history);
        $eventManager = EventManager::getInstance();
        $eventManager->detach(Event::NAME);
        $eventManager->attach(Event::NAME, function (EventInterface $event){
            TakeTileEventSubscriber::onTakenTile($event);
        });
        $game = new TicTacToe(new Board(), $history, new PlayersFactory(),
            new FindWinner(),
            $eventManager,
            \uniqid()
        );
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $game->players();
        $playerX->takeTile(new Tile(2, 2), $game, $history);
        $player0->takeTile(new Tile(1, 1), $game, $history);
        $playerX->takeTile(new Tile(0, 0), $game, $history);
        $player0->takeTile(new Tile(0, 1), $game, $history);
        $playerX->takeTile(new Tile(0, 2), $game, $history);
        $player0->takeTile(new Tile(2, 1), $game, $history);
        $this->assertSame($player0, $game->winner());
    }
}
