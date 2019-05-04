<?php
declare(strict_types=1);

namespace App\Tests\integration\business;

use App\Core\Application\Event\EventManager;
use App\Core\Domain\Model\TicTacToe\AI\AI;
use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\History;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Service\FindWinner;
use App\Core\Domain\Service\PlayersFactory;
use PHPUnit\Framework\TestCase;

class PlayingAgainstAISimulationTest extends TestCase
{
    /** @var  TicTacToe $game */
    private $game;

    /**
     * @test
     */
    public function random_looped_taken_tiles_should_fill_whole_board()
    {
        $eventManager = EventManager::getInstance();
        $history = new History();
        $game = new TicTacToe(new Board(), $history, new PlayersFactory(), new FindWinner(),
            $eventManager,
            \uniqid()
            );
        $this->game = $game;
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $game->players();
        $ai = new AI($game);
        for ($i = 2; $i <= 9; $i += 2) {
            $playerX->takeTile($ai->takeRandomFreeTile(), $game, $history);
            /** @var Player $player0 */
            $player0->takeTile($this->simulate_choosing_tiles_of_real_player(), $game, $history);
        }

        self::assertTrue(
            \is_null($game->winner()) ||
            $game->winner()->symbol()->value() === 'X' ||
            $game->winner()->symbol()->value() === '0'
        );
        self::assertTrue(
            \array_reduce(
                $game->board()->contents(),
                function ($carry, $value) {
                    $carry = $carry || (\is_null($value) === false);

                    return $carry;
                },
                false
            )
        );
    }

    private function simulate_choosing_tiles_of_real_player()
    {
        $ai = new AI($this->game);

        return $ai->takeRandomFreeTile();
    }
}
