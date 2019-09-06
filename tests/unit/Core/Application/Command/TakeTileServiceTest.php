<?php
declare(strict_types=1);

namespace App\Tests\unit\Core\Application\Command;

use App\Core\Application\Command\TakeTileService;
use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Board\Board;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Core\Domain\Service\History\HistoryInterface;
use App\Core\Domain\Service\TurnControl\TurnControl;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class TakeTileServiceTest
 * @package App\Tests\unit
 */
class TakeTileServiceTest extends TestCase
{
    private $tileProphecy;
    private $boardProphecy;
    private $gameProphecy;
    private $historyProphecy;
    private $turnControlProphecy;
    private $playerXProphecy;
    private $playerOProphecy;

    protected function setUp()
    {
        list($this->playerXProphecy, $this->playerOProphecy) = $this->preparePlayers();
        $this->tileProphecy = $this->prophesize(Tile::class);

        $this->boardProphecy = $this->prophesize(Board::class);
        $this->boardProphecy->getPlayer($this->tileProphecy->reveal())->willReturn($this->playerXProphecy->reveal());

        $this->gameProphecy = $this->prepareGame($this->boardProphecy);

        $this->historyProphecy = $this->prophesize(HistoryInterface::class);
        $this->historyProphecy->lastItem($this->gameProphecy->reveal())->willReturn(null);
        $this->historyProphecy->getStartingPlayerSymbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        $this->prepareSavingTurn($this->historyProphecy, $this->playerXProphecy, $this->tileProphecy, $this->gameProphecy);

        $this->turnControlProphecy = $turnControlProphecy = $this->prophesize(TurnControl::class);
    }

    /**
     * @test
     */
    public function initBoard()
    {
        self::assertSame($this->boardProphecy->reveal(), $this->gameProphecy->reveal()->board());
    }

    /**
     * @test
     */
    public function markBoard()
    {
        // Given
        $this->boardProphecy->mark($this->tileProphecy->reveal(), $this->playerXProphecy->reveal())->shouldBeCalled();
        $service = new TakeTileService(
            $this->gameProphecy->reveal(),
            $this->historyProphecy->reveal(),
            $this->turnControlProphecy->reveal()
        );

        // When
        $service->takeTile($this->playerXProphecy->reveal(), $this->tileProphecy->reveal());

        // Then
        self::assertNotEmpty($this->gameProphecy->reveal()->board()->getPlayer($this->tileProphecy->reveal()));
        self::assertSame($this->playerXProphecy->reveal(),
            $this->gameProphecy->reveal()->board()->getPlayer($this->tileProphecy->reveal()));
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
     * @return Game|ObjectProphecy
     */
    private function prepareGame($boardProphecy)
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
