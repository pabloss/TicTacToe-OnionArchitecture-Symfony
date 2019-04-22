<?php
declare(strict_types=1);

namespace App\Tests\integration\History\Framework;

use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use App\Presentation\Web\Pub\History\History;
use App\Tests\Stubs\History\HistoryItem;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class HistoryTest
 * @package App\Tests\integration\History\Framework
 * @todo:  usuwamy set z historii i zamieniamy na saveMovement
 */
class HistoryTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @test
     */
    public function history_should_implement_interface_from_domain()
    {
        self::assertInstanceOf(HistoryInterface::class, self::$container->get(History::class));
    }

    /**
     * @test
     * @description: zapisanie playera, jego ruchu i gry pozwala odczytać go z historii
     * @todo: jednoczesnie ostatni ruch w jednej grze to inny ruch w innej grze
     */
    public function playerMovementForGameShouldBeSaved()
    {
        $history = new History($this->entityManager->getRepository(\App\Entity\History::class));
        $gameProphecy = $this->prophesize(Game::class);
        $gameProphecy->uuid()->willReturn('0');
        $game = $gameProphecy->reveal();

        $player0 = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), \uniqid());
        $playerX = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), \uniqid());
        $tile1 = new Tile(0, 0);
        $tile2 = new Tile(1, 1);

        $history->saveTurn($player0, $tile1, $game);
        $history->saveTurn($playerX, $tile2, $game);

        $historyItem = $history->getLastTurn($game);
        self::assertInstanceOf(HistoryItem::class, $historyItem);
        self::assertSame('0', $game->uuid());
        self::assertEquals($playerX, $historyItem->player());
        self::assertEquals($tile2, $historyItem->tile());
        self::assertEquals($game, $historyItem->game());

        $historyItem = $history->getTurn($game, 1);
        self::assertInstanceOf(HistoryItem::class, $historyItem);
        self::assertEquals($player0, $historyItem->player());
        self::assertEquals($tile1, $historyItem->tile());
        self::assertEquals($game, $historyItem->game());

        $tile3 = new Tile(2, 2);
        $gameProphecy2 = $this->prophesize(Game::class);
        $gameProphecy2->uuid()->willReturn('1');
        $game2 = $gameProphecy2->reveal();
        $history->saveTurn($player0, $tile3, $game2);
        $history->saveTurn($player0, $tile1, $game);
        $historyItem = $history->getLastTurn($game2);
        self::assertSame('1', $game2->uuid());
        self::assertInstanceOf(HistoryItem::class, $historyItem);
        self::assertEquals($player0, $historyItem->player());
        self::assertEquals($tile3, $historyItem->tile());
        self::assertEquals($game2, $historyItem->game());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}

