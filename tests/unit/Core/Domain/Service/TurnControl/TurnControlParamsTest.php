<?php
declare(strict_types=1);

namespace App\Tests\unit\Core\Domain\Service\TurnControl;

use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Board\TileInterface;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\GameInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\PlayerInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Core\Domain\Service\History\HistoryInterface;
use App\Core\Domain\Service\TurnControl\Params;
use App\Tests\Stubs\History\History;
use PHPUnit\Framework\TestCase;

class TurnControlParamsTest extends TestCase
{
    /**
     * @test
     */
    public function getParameters()
    {
        // create Player X
        $playerXProphecy = $this->prophesize(PlayerInterface::class);
        $playerXProphecy->uuid()->willReturn(0);
        $playerXProphecy->symbolValue()->willReturn(Symbol::PLAYER_X_SYMBOL);
        // create Player O
        $playerOProphecy = $this->prophesize(PlayerInterface::class);
        $playerOProphecy->uuid()->willReturn(1);
        $playerOProphecy->symbolValue()->willReturn(Symbol::PLAYER_0_SYMBOL);
        // create game
        $gameProphecy = $this->prophesize(GameInterface::class);
        $gameProphecy->uuid()->willReturn(2);

        $tileProphecy = $this->prophesize(TileInterface::class);
        $historyProphecy = $this->prophesize(HistoryInterface::class);


        $params = new Params($playerOProphecy->reveal(), $tileProphecy->reveal(), $gameProphecy->reveal(), $historyProphecy->reveal());

        self::assertSame($playerOProphecy->reveal(), $params->player());
        self::assertSame($gameProphecy->reveal(), $params->game());
        self::assertSame($tileProphecy->reveal(), $params->tile());
        self::assertSame($historyProphecy->reveal(), $params->history());
    }
}
