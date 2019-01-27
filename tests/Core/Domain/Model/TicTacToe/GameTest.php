<?php
declare(strict_types=1);

namespace AppTests\Core\Domain\Model\TicTacToe;

use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\History;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Service\FindWinner;
use App\Core\Domain\Service\PlayersFactory;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{

    /**
     * @test
     */
    public function game_should_record_correct_turns()
    {
        $game = new TicTacToe(new Board(), new History(), new PlayersFactory(), new FindWinner());
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $game->players();
        $playerX->takeTile(new \App\Core\Domain\Model\TicTacToe\ValueObject\Tile(0, 0), $game);
        $player0->takeTile(new \App\Core\Domain\Model\TicTacToe\ValueObject\Tile(0, 1), $game);
        $playerX->takeTile(new \App\Core\Domain\Model\TicTacToe\ValueObject\Tile(1, 0), $game);
        $player0->takeTile(new \App\Core\Domain\Model\TicTacToe\ValueObject\Tile(1, 1), $game);
        self::assertEquals([$playerX, $player0, null, $playerX, $player0, null, null, null, null], $game->board()->contents());
        self::assertEquals([[0, 0], [0, 1], [1, 0], [1, 1]], $game->history()->content());
        self::assertEquals($game::OK, $game->errors());
    }

    /**
     * @test
     */
    public function game_should_not_produce_new_players_if_ones_already_exist()
    {
        $game = new TicTacToe(new Board(), new History(), new PlayersFactory(), new FindWinner());
        list(Symbol::PLAYER_X_SYMBOL => $playerX1, Symbol::PLAYER_0_SYMBOL => $player01) = $game->players();
        list(Symbol::PLAYER_X_SYMBOL => $playerX2, Symbol::PLAYER_0_SYMBOL => $player02) = $game->players();

        self::assertSame($playerX1, $playerX2);
        self::assertSame($player01, $player02);
    }
}
