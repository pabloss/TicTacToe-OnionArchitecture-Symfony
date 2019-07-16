<?php
declare(strict_types=1);

namespace App\Tests\unit;

use App\Core\Application\Command\TakeTileService;
use App\Core\Application\Validation\TurnControl;
use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Board\Board;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class TakeTileServiceTest
 * @package App\Tests\unit
 */
class TakeTileServiceTest extends TestCase
{
    /**
     * @test
     */
    public function initBoard()
    {
        list($playerXProphecy, $playerOProphecy) = $this->preparePlayers();
        $tileProphecy = $this->prophesize(Tile::class);

        $boardProphecy = $this->prophesize(Board::class);
        $boardProphecy->getPlayer($tileProphecy->reveal())->willReturn($playerXProphecy->reveal());

        $gameProphecy = $this->prepareGame($boardProphecy, $playerOProphecy, $playerXProphecy);

        self::assertSame($boardProphecy->reveal(), $gameProphecy->reveal()->board());
    }

    /**
     * @test
     */
    public function markBoard()
    {
        list($playerXProphecy, $playerOProphecy) = $this->preparePlayers();
        $tileProphecy = $this->prophesize(Tile::class);

        $boardProphecy = $this->prophesize(Board::class);
        $boardProphecy->getPlayer($tileProphecy->reveal())->willReturn($playerXProphecy->reveal());
        $boardProphecy->mark($tileProphecy->reveal(), $playerXProphecy->reveal())->shouldBeCalled();

        $gameProphecy = $this->prepareGame($boardProphecy, $playerOProphecy, $playerXProphecy);

        $historyProphecy = $this->prophesize(HistoryInterface::class);
        $historyProphecy->lastItem($gameProphecy->reveal())->willReturn(null);
        $historyProphecy->getStartingPlayerSymbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        $this->prepareSavingTurn($historyProphecy, $playerXProphecy, $tileProphecy, $gameProphecy);

        $turnControlProphecy = $this->prophesize(TurnControl::class);

        $service = new TakeTileService($gameProphecy->reveal(), $historyProphecy->reveal(), $turnControlProphecy->reveal());
        $service->takeTile($playerXProphecy->reveal(), $tileProphecy->reveal());

        self::assertNotEmpty($gameProphecy->reveal()->board()->getPlayer($tileProphecy->reveal()));
        self::assertSame($playerXProphecy->reveal(), $gameProphecy->reveal()->board()->getPlayer($tileProphecy->reveal()));
    }



    /**
     * @return array
     * @throws NotAllowedSymbolValue
     */
    private function preparePlayers(): array
    {
        $playerOProphecy = $this->prophesize(Player::class);
        $playerOProphecy->uuid()->willReturn(0);
        $playerOProphecy->symbolValue()->willReturn(Symbol::PLAYER_0_SYMBOL);
        $playerOProphecy->willBeConstructedWith([
            new Symbol(Symbol::PLAYER_0_SYMBOL),
            0
        ]);
        $playerXProphecy = $this->prophesize(Player::class);
        $playerXProphecy->uuid()->willReturn(1);
        $playerXProphecy->symbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        $playerXProphecy->willBeConstructedWith([
            new Symbol(Symbol::PLAYER_X_SYMBOL),
            1
        ]);
        return array($playerXProphecy, $playerOProphecy);
    }

    /**
     * @param $boardProphecy
     * @param $playerOProphecy
     * @param $playerXProphecy
     * @return Game|ObjectProphecy
     * @throws NotAllowedSymbolValue
     */
    private function prepareGame($boardProphecy, $playerOProphecy, $playerXProphecy)
    {
        $gameProphecy = $this->prophesize(Game::class);
        $gameProphecy->board()->willReturn($boardProphecy->reveal());
        return $gameProphecy;
    }

    /**
     * @param $historyProphecy
     * @param $playerOProphecy
     * @param $tileProphecy
     * @param $gameProphecy
     */
    private function prepareSavingTurn($historyProphecy, $playerOProphecy, $tileProphecy, $gameProphecy): void
    {
        $historyProphecy->saveTurn($playerOProphecy->reveal(), $tileProphecy->reveal(), $gameProphecy->reveal());
    }
}
