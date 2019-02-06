<?php
declare(strict_types=1);

namespace App\Tests\integration\Event;

use App\Core\Domain\Event\EventManagerInterface;
use App\Core\Domain\Event\Params\Params;
use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use App\Core\Domain\Service\FindWinner;
use App\Core\Domain\Service\PlayersFactory;
use App\Presentation\Web\Pub\Event\Event;
use App\Presentation\Web\Pub\Event\EventManager;
use App\Presentation\Web\Pub\History\History;
use App\Repository\HistoryRepository;
use App\Tests\Stubs\History\HistoryItem;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventManagerTest extends WebTestCase
{
    /**
     * @test
     */
    public function eventManagerShouldImplementEventManagerInterface()
    {
        $kernel = self::bootKernel();

        $eventManager = $kernel->getContainer()->get('App\Presentation\Web\Pub\Event\EventManager');
        self::assertInstanceOf(EventManagerInterface::class, $eventManager);
    }

    /**
     * @test
     */
    public function triggeringEventShoulBeCatchedByCorecctSubscriber()
    {
        $kernel = self::bootKernel();
        $history = new History(self::$container->get(HistoryRepository::class));
        $playersFactory = $this->prophesize(PlayersFactory::class);

        $eventManager = $kernel->getContainer()
            ->get('App\Presentation\Web\Pub\Event\EventManager');
        $playersFactory->create()->willReturn([
            Symbol::PLAYER_X_SYMBOL => new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), \uniqid()),
            Symbol::PLAYER_0_SYMBOL => new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), \uniqid()),
        ]);
        $game = new Game($this->prophesize(Board::class)->reveal(), $history,
            $playersFactory->reveal(), $this->prophesize(FindWinner::class)->reveal(), $eventManager, \uniqid());

        $kernel = self::bootKernel();

        $eventManager = $kernel->getContainer()->get('App\Presentation\Web\Pub\Event\EventManager');
        $eventManager->trigger(Event::NAME, new Params($game->players()[Symbol::PLAYER_X_SYMBOL], new Tile(0,0), $game));
        /** @var HistoryItem $history */
        $history = $game->history()->getLastTurn($game);
        self::assertEquals(new Tile(0, 0), $history->tile());
    }
}
