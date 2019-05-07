<?php
declare(strict_types=1);

namespace App\Tests\integration\Event;

use App\Core\Domain\Event\EventManagerInterface;
use App\Core\Domain\Event\Params\Params;
use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use App\Core\Domain\Service\FindWinner;
use App\Core\Domain\Service\PlayersFactory;
use App\Presentation\Web\Pub\Event\Event;
use App\Presentation\Web\Pub\Event\EventManager;
use App\Presentation\Web\Pub\History\History;
use App\Repository\HistoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class EventManagerTest
 * @package App\Tests\integration\Event
 */
class EventManagerTest extends WebTestCase
{
    /**
     * @test
     */
    public function eventManagerImplementsEventManagerInterface()
    {
        // Given
        $kernel = self::bootKernel();

        // When
        $eventManager = $kernel->getContainer()->get(EventManager::class);

        // Then
        self::assertInstanceOf(EventManagerInterface::class, $eventManager);
    }

    /**
     * @test
     */
    public function triggeringEventCaughtByCorrectSubscriber()
    {
        // Given
        $kernel = self::bootKernel();
        $history = self::$container->get(History::class) ;
        $playersFactory = $this->prophesize(PlayersFactory::class);
        $playersFactory->create()->willReturn([
            Symbol::PLAYER_X_SYMBOL => new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), \uniqid()),
            Symbol::PLAYER_0_SYMBOL => new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), \uniqid()),
        ]);
        $eventManager = $kernel->getContainer()->get(EventManager::class);

        $game = new Game(
            $this->prophesize(Board::class)->reveal(),
            $history,
            $playersFactory->reveal(),
            $this->prophesize(FindWinner::class)->reveal(),
            $eventManager,
            \uniqid()
        );

        // When
        $eventManager->trigger(
            Event::NAME, new Params($game->players()[Symbol::PLAYER_X_SYMBOL],
            new Tile(0,0), $game, $history)
        );

        // Then
        /** @var \App\Core\Application\History\HistoryItem $historyItem */
        $historyItem = $history->lastItem($game);
        self::assertEquals($game, $historyItem->game());
        self::assertEquals($game->players()[Symbol::PLAYER_X_SYMBOL], $historyItem->player());
        self::assertEquals(new Tile(0, 0), $historyItem->tile());
    }
}
