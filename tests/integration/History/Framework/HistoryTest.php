<?php
declare(strict_types=1);

namespace App\Tests\integration\History\Framework;

use App\Core\Application\Service\History\HistoryContent;
use App\Core\Application\Service\History\HistoryItem;
use App\Core\Application\Service\History\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Entity\History as HistoryEntity;
use App\Presentation\Web\Pub\History\History;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class HistoryTest
 * @package App\Tests\integration\History\Framework
 */
class HistoryTest extends WebTestCase
{

    /**
     * @var EntityManager
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
     */
    public function get_last_turn()
    {
        // Given
        $history = new History($this->entityManager->getRepository(HistoryEntity::class));

        $gameProphecy = $this->prophesize(Game::class);
        $gameProphecy->uuid()->willReturn('0');
        $player = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), \uniqid());
        $tile = new Tile(0,0);

        // When
        $history->saveTurn(
            $player,
            $tile,
            $gameProphecy->reveal()
        );

        // Then
        $historyItem = $history->lastItem($gameProphecy->reveal());
        self::assertEquals($gameProphecy->reveal(), $historyItem->game());
        self::assertEquals($player, $historyItem->player());
        self::assertEquals($tile, $historyItem->tile());
    }

    /**
     * @test
     * todo: napisz dla dwóch gier
     */
    public function history_should_return_contents()
    {
        // Given
        $historyRepository = $this->entityManager->getRepository(HistoryEntity::class);
        $qb = $historyRepository->createQueryBuilder('h');
        $qb->delete()->getQuery()->execute();
        $history = new History($historyRepository);

        $expectedContent = [];
        $gameProphecy = $this->prophesize(Game::class);

        $gameProphecy->uuid()->willReturn('0');

        $players = [];
        $players[] =  new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), \uniqid());
        $players[] = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), \uniqid());

        // When
        for ($i = 0; $i < History::LIMIT; $i++) {
            $tile = new Tile(\rand(0,2),\rand(0,2));
            $value = new HistoryItem(
                $players[$i%2],
                $tile,
                $gameProphecy->reveal()
            );
            $expectedContent[$history->length($gameProphecy->reveal()) % History::LIMIT] = $value;
            $history->saveTurn(
                $players[$i%2],
                $tile,
                $gameProphecy->reveal()
            );
        }

        // Then
        self::assertEquals(new HistoryContent($expectedContent), $history->content($gameProphecy->reveal()));

        $randIndexInHistory = History::LIMIT - \rand(1, History::LIMIT);
        $randomExpectedHistoryItem = (new HistoryContent($expectedContent))->getArrayCopy()[$randIndexInHistory];
        $randomActualHistoryItem = $history->content($gameProphecy->reveal())->getArrayCopy()[$randIndexInHistory];

        self::assertEquals($randomExpectedHistoryItem, $randomActualHistoryItem);
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

