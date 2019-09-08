<?php
declare(strict_types=1);

namespace App\Tests\integration\Core\Domain\Service\History\Framework;

use App\AppCore\DomainModel\Game\Board\Tile;
use App\AppCore\DomainModel\Game\Game;
use App\AppCore\DomainModel\Game\Player\Player;
use App\AppCore\DomainModel\Game\Player\Symbol;
use App\AppCore\DomainModel\History\History;
use App\AppCore\DomainModel\History\HistoryContent;
use App\AppCore\DomainModel\History\HistoryInterface;
use App\AppCore\DomainModel\History\HistoryItem;
use App\Entity\History as HistoryEntity;
use App\Repository\HistoryRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class HistoryTest
 * @package App\Tests\integration\Core\Domain\Service\History\Framework
 */
class HistoryTest extends WebTestCase
{

    /**
     * @var EntityManager
     */
    private $entityManager;

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
        $player = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), uniqid());
        $tile = new Tile(0, 0);

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
        /** @var HistoryRepository $historyRepository */
        $historyRepository = $this->entityManager->getRepository(HistoryEntity::class);
        $historyRepository->cleanupRepository();
        $history = new History($historyRepository);

        $expectedContent = [];
        $gameProphecy = $this->prophesize(Game::class);

        $gameProphecy->uuid()->willReturn('0');

        $players = [];
        $players[] = new Player(new Symbol(Symbol::PLAYER_X_SYMBOL), uniqid());
        $players[] = new Player(new Symbol(Symbol::PLAYER_0_SYMBOL), uniqid());

        // When
        for ($i = 0; $i < History::LIMIT; $i++) {
            $tile = new Tile(rand(0, 2), rand(0, 2));
            $value = new HistoryItem(
                $players[$i % 2],
                $tile,
                $gameProphecy->reveal()
            );
            $expectedContent[$history->length($gameProphecy->reveal()) % History::LIMIT] = $value;
            $history->saveTurn(
                $players[$i % 2],
                $tile,
                $gameProphecy->reveal()
            );
        }

        // Then
        self::assertEquals(new HistoryContent($expectedContent), $history->content($gameProphecy->reveal()));

        $randIndexInHistory = History::LIMIT - rand(1, History::LIMIT);
        $randomExpectedHistoryItem = (new HistoryContent($expectedContent))->getArrayCopy()[$randIndexInHistory];
        $randomActualHistoryItem = $history->content($gameProphecy->reveal())->getArrayCopy()[$randIndexInHistory];

        self::assertEquals($randomExpectedHistoryItem, $randomActualHistoryItem);
    }

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
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}

