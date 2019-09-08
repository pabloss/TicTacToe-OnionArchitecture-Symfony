<?php
declare(strict_types=1);

namespace App\Tests\unit\Presentation\Web\Pub\Service;

use App\AppCore\ApplicationServices\FormatHistoryResult;
use App\AppCore\DomainModel\Game\Board\Tile;
use App\AppCore\DomainModel\Game\Game;
use App\AppCore\DomainModel\Game\Player\Player;
use App\AppCore\DomainModel\Game\Player\Symbol;
use App\AppCore\DomainModel\History\HistoryContent;
use App\AppCore\DomainModel\History\HistoryItem;
use App\AppCore\DomainModel\History\HistoryRepositoryInterface;
use App\Entity\History;
use PHPUnit\Framework\TestCase;

class FormatHistoryResultTest extends TestCase
{
    /**
     * @test
     */
    public function result()
    {
        $histories = [
            (new History())->setPlayerSymbol(Symbol::PLAYER_X_SYMBOL)->setTile(array(0, 0)),
            (new History())->setPlayerSymbol(Symbol::PLAYER_0_SYMBOL)->setTile(array(0, 1)),
            (new History())->setPlayerSymbol(Symbol::PLAYER_0_SYMBOL)->setTile(array(1, 0)),
            (new History())->setPlayerSymbol(Symbol::PLAYER_X_SYMBOL)->setTile(array(1, 1)),
            (new History())->setPlayerSymbol(Symbol::PLAYER_X_SYMBOL)->setTile(array(1, 1)),
            (new History())->setPlayerSymbol(Symbol::PLAYER_0_SYMBOL)->setTile(array(1, 1)),
            (new History())->setPlayerSymbol(Symbol::PLAYER_X_SYMBOL)->setTile(array(2, 1)),
            (new History())->setPlayerSymbol(Symbol::PLAYER_0_SYMBOL)->setTile(array(1, 2)),
            (new History())->setPlayerSymbol(Symbol::PLAYER_X_SYMBOL)->setTile(array(2, 2)),
            (new History())->setPlayerSymbol(Symbol::PLAYER_X_SYMBOL)->setTile(array(2, 1)),
            (new History())->setPlayerSymbol(Symbol::PLAYER_0_SYMBOL)->setTile(array(0, 2)),
            (new History())->setPlayerSymbol(Symbol::PLAYER_X_SYMBOL)->setTile(array(1, 1)),
        ];
        $gameProphecy = $this->prophesize(Game::class);
        $gameProphecy->uuid()->willReturn(0);

        $result = new HistoryContent();
        /** @var History $historyEntity */
        foreach ($histories as $index => $historyEntity){
            $result->append(
                new HistoryItem(
                    new Player(new Symbol($historyEntity->getPlayerSymbol()), (string) ($index%2)),
                    new Tile($historyEntity->getTile()[0], $historyEntity->getTile()[1]),
                    $gameProphecy->reveal()
                )
            );
        }

        $historyRepositoryProphecy = $this->prophesize(HistoryRepositoryInterface::class);
        $historyRepositoryProphecy->getByGame($gameProphecy->reveal())->willReturn($result);

        $service = new FormatHistoryResult($historyRepositoryProphecy->reveal());

        $expectedResult = [];
        /** @var History $history */
        foreach ($histories as $history){
            $expectedResult[$history->getTile()[0]*3+$history->getTile()[1]] = $history->getPlayerSymbol();
        }

        self::assertEquals($expectedResult, $service->format($gameProphecy->reveal()));
    }
}
