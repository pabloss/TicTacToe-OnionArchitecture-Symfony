<?php
declare(strict_types=1);

namespace App\Tests\unit\Presentation\Web\Pub\Service;

use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Entity\History;
use App\Presentation\Web\Pub\Service\FormatHistoryResult;
use App\Repository\HistoryRepository;
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

        $historyRepositoryProphecy = $this->prophesize(HistoryRepository::class);
        $historyRepositoryProphecy->findBy([], ['createdAt' => 'DESC'])->willReturn($histories);

        $service = new FormatHistoryResult($historyRepositoryProphecy->reveal());

        $expectedResult = [];
        /** @var History $history */
        foreach ($histories as $history){
            $expectedResult[$history->getTile()[0]*3+$history->getTile()[1]] = $history->getPlayerSymbol();
        }

        self::assertEquals($expectedResult, $service->format());
    }
}
