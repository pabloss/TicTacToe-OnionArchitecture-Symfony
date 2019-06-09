<?php
declare(strict_types=1);

namespace App\Tests\unit;

use App\Core\Application\Service\TakeTileService;
use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use PHPUnit\Framework\TestCase;

/**
 * Class TakeTileServiceTest
 * @package App\Tests\unit
 */
class TakeTileServiceTest extends TestCase
{
    /**
     * @test
     */
    public function board()
    {
        $playerXProphecy = $this->prophesize(Player::class);
        $playerOProphecy = $this->prophesize(Player::class);
        $playerOProphecy->uuid()->willReturn(0);
        $playerOProphecy->symbolValue()->willReturn(Symbol::PLAYER_0_SYMBOL);
        $playerOProphecy->willBeConstructedWith([
            new Symbol(Symbol::PLAYER_0_SYMBOL),
            0
        ]);
        $playerXProphecy->uuid()->willReturn(1);
        $playerXProphecy->symbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        $tileProphecy = $this->prophesize(Tile::class);
        $playerXProphecy->willBeConstructedWith([
            new Symbol(Symbol::PLAYER_X_SYMBOL),
            1
        ]);
        $boardProphecy = $this->prophesize(Board::class);
        $boardProphecy->getPlayer($tileProphecy->reveal())->willReturn($playerXProphecy->reveal());
        $boardProphecy->mark($tileProphecy->reveal(), $playerXProphecy->reveal())->shouldBeCalled();
        $gameProphecy = $this->prophesize(Game::class);
        $gameProphecy->board()->willReturn($boardProphecy->reveal());
        $gameProphecy->players()->willReturn([
            $playerOProphecy->reveal(),
            $playerXProphecy->reveal(),
        ]);
        $historyProphecy = $this->prophesize(HistoryInterface::class);
        $historyProphecy->lastItem($gameProphecy->reveal())->willReturn(null);
        $historyProphecy->saveTurn($playerXProphecy->reveal(), $tileProphecy->reveal(), $gameProphecy->reveal());
        $historyProphecy->getStartingPlayerSymbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        $service = new TakeTileService($gameProphecy->reveal(), $historyProphecy->reveal());

        self::assertSame($boardProphecy->reveal(), $gameProphecy->reveal()->board());

        $service->takeTile($playerXProphecy->reveal(), $tileProphecy->reveal());
        self::assertNotEmpty($gameProphecy->reveal()->board()->getPlayer($tileProphecy->reveal()));
        self::assertSame($playerXProphecy->reveal(), $gameProphecy->reveal()->board()->getPlayer($tileProphecy->reveal()));
    }

    /**
     * @test
     */
    public function hasError()
    {
        $playerXProphecy = $this->prophesize(Player::class);
        $playerOProphecy = $this->prophesize(Player::class);
        $playerOProphecy->uuid()->willReturn(0);
        $playerOProphecy->symbolValue()->willReturn(Symbol::PLAYER_0_SYMBOL);
        $playerOProphecy->willBeConstructedWith([
            new Symbol(Symbol::PLAYER_0_SYMBOL),
            0
        ]);
        $playerXProphecy->uuid()->willReturn(1);
        $playerXProphecy->symbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        $tileProphecy = $this->prophesize(Tile::class);
        $playerXProphecy->willBeConstructedWith([
            new Symbol(Symbol::PLAYER_X_SYMBOL),
            1
        ]);
        $boardProphecy = $this->prophesize(Board::class);
        $gameProphecy = $this->prophesize(Game::class);
        $gameProphecy->board()->willReturn($boardProphecy->reveal());
        $gameProphecy->players()->willReturn([
            $playerOProphecy->reveal(),
            $playerXProphecy->reveal(),
        ]);
        $gameProphecy->addError(Game::GAME_STARTED_BY_PLAYER0_ERROR, $playerOProphecy->reveal())->shouldBeCalled();
        $gameProphecy->addError(Game::GAME_STARTED_BY_PLAYER0_ERROR, $playerXProphecy->reveal());
        $gameProphecy->errors()->willReturn(Game::GAME_STARTED_BY_PLAYER0_ERROR);
        $historyProphecy = $this->prophesize(HistoryInterface::class);
        $historyProphecy->lastItem($gameProphecy->reveal())->willReturn(null);
        $historyProphecy->saveTurn($playerOProphecy->reveal(), $tileProphecy->reveal(), $gameProphecy->reveal());
        $historyProphecy->getStartingPlayerSymbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        $service = new TakeTileService($gameProphecy->reveal(), $historyProphecy->reveal());

        self::assertSame($boardProphecy->reveal(), $gameProphecy->reveal()->board());

        $service->takeTile($playerOProphecy->reveal(), $tileProphecy->reveal());
        self::assertTrue($service->hasError(Game::GAME_STARTED_BY_PLAYER0_ERROR));
    }

    /**
     * by pozbyć się powtórzeń z hasError mogę użyć Helpera: TurnControl, ale do tego jest mi potrezbna historia
     * @test
     */
    public function trackHistory()
    {
        $gameProphecy = $this->prophesize(Game::class);
        $boardProphecy = $this->prophesize(Board::class);
        $playerXProphecy = $this->prophesize(Player::class);
        $playerOProphecy = $this->prophesize(Player::class);
        $tileProphecy = $this->prophesize(Tile::class);

        $playerXProphecy->symbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);

        $playerOProphecy->uuid()->willReturn(0);
        $playerOProphecy->symbolValue()->willReturn(Symbol::PLAYER_0_SYMBOL);

        $gameProphecy->board()->willReturn($boardProphecy->reveal());
        $gameProphecy->players()->willReturn([
            $playerOProphecy->reveal(),
            $playerXProphecy->reveal(),
        ]);
        $gameProphecy->addError(Game::GAME_STARTED_BY_PLAYER0_ERROR, $playerOProphecy->reveal())->shouldBeCalled();
        $gameProphecy->errors()->willReturn(Game::GAME_STARTED_BY_PLAYER0_ERROR);

        $historyProphecy = $this->prophesize(HistoryInterface::class);
        $historyProphecy->lastItem($gameProphecy->reveal())->willReturn(null);
        $historyProphecy->saveTurn($playerOProphecy->reveal(), $tileProphecy->reveal(), $gameProphecy->reveal())->shouldBeCalled();
        $historyProphecy->getStartingPlayerSymbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        $service = new TakeTileService($gameProphecy->reveal(), $historyProphecy->reveal());

        self::assertSame($boardProphecy->reveal(), $gameProphecy->reveal()->board());

        $service->takeTile($playerOProphecy->reveal(), $tileProphecy->reveal());
    }
}
