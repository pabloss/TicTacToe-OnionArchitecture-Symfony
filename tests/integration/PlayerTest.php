<?php
declare(strict_types=1);

namespace AppTests\integration;

use App\Core\Domain\Model\TicTacToe\AI\AI;
use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\History;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Service\FindWinner;
use App\Core\Domain\Service\PlayersFactory;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    /** @var  TicTacToe $game */
    private $game;

    /**
     * @test
     */
    public function looping_AI_player_fills_whole_board_in_9_turns()
    {
        $game = new TicTacToe(new Board(), new History(), new PlayersFactory(), new FindWinner());
        $ai = new AI($game);
        $this->game = $game;
        list(Symbol::PLAYER_X_SYMBOL => $player, Symbol::PLAYER_0_SYMBOL => $notUsedPlayer) = $game->players();
        for (
            $expectedFilledCount = 2;
            $expectedFilledCount <= 9;
            $expectedFilledCount += 2
        ) {
            $player->takeTile($ai->takeRandomFreeTile(), $game);
            $notUsedPlayer->takeTile($this->simulate_choosing_tiles_of_real_player(), $game);
            $actualFilledCount = \array_reduce(
                $game->board()->contents(),
                function ($carry, $item) {
                    if (\is_null($item) === false) {
                        $carry++;
                    }

                    return $carry;
                },
                0
            );

            $allFreeTileIndexes = [];
            $allFilledTileIndexes = [];
            \array_walk(
                $game->board()->contents(),
                function ($value, $key) use (&$allFreeTileIndexes, &$allFilledTileIndexes) {
                    if (\is_null($value) === true) {
                        $allFreeTileIndexes[] = $key;
                    } else {
                        $allFilledTileIndexes[] = $key;
                    }
                }
            );
            self::assertEquals(0, \count(\array_intersect($allFreeTileIndexes, $allFilledTileIndexes)));
            self::assertEquals(9, \count($allFreeTileIndexes) + \count($allFilledTileIndexes));
            self::assertEquals(
                $expectedFilledCount,
                $actualFilledCount
            );
        }
    }

    private function simulate_choosing_tiles_of_real_player()
    {
        $ai = new AI($this->game);

        return $ai->takeRandomFreeTile();
    }
}
