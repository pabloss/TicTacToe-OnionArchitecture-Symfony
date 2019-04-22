<?php
declare(strict_types=1);

namespace App\Core\Domain\Service;

use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;

/**
 * Winner is found when all his/her marks are in one line at some stage of game
 *
 * Lines are defined in patterns.
 *
 * We need symbol that we try to find, board and patterns of every winning result.
 *
 * Class FindWinner
 * @package App\Core\Domain\Validation
 */
class FindWinner
{
    const patterns = [
        [
            '#',
            '#',
            '#',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
        ],
        [
            ' ',
            ' ',
            ' ',
            '#',
            '#',
            '#',
            ' ',
            ' ',
            ' ',
        ],
        [
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            '#',
            '#',
            '#',
        ],
        [
            '#',
            ' ',
            ' ',
            '#',
            ' ',
            ' ',
            '#',
            ' ',
            ' ',
        ],
        [
            ' ',
            '#',
            ' ',
            ' ',
            '#',
            ' ',
            ' ',
            '#',
            ' ',
        ],
        [
            ' ',
            ' ',
            '#',
            ' ',
            ' ',
            '#',
            ' ',
            ' ',
            '#',
        ],
        [
            '#',
            ' ',
            ' ',
            ' ',
            '#',
            ' ',
            ' ',
            ' ',
            '#',
        ],
        [
            ' ',
            ' ',
            '#',
            ' ',
            '#',
            ' ',
            '#',
            ' ',
            ' ',
        ],
    ];

    /**
     * We'll operate on reference to control a state of board
     *
     * @var Board
     */
    private $board;

    /**
     * @param Game $game
     * @return \App\Core\Domain\Model\TicTacToe\ValueObject\Player|null
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
     */
    public function winner(Game $game): ?Player
    {
        return
            $this->findWinnerByBoardPatterns(new Symbol(Symbol::PLAYER_X_SYMBOL), $game) ??
            $this->findWinnerByBoardPatterns(new Symbol(Symbol::PLAYER_0_SYMBOL), $game);
    }

    /**
     * @param \App\Core\Domain\Model\TicTacToe\ValueObject\Symbol $symbol
     * @param Game $game
     * @return \App\Core\Domain\Model\TicTacToe\ValueObject\Player|null
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
     */
    private function findWinnerByBoardPatterns(Symbol $symbol, Game $game): ?Player
    {
        $this->board = $game->board();
        // Here were loop, but now I've changed to use native PHP array function
        // I'm not sure if it "improves" performance
        $found = \array_reduce(
            self::patterns,
            function ($carry, $pattern) use ($symbol) {
                $carry =
                    (
                        $carry ||
                        $this->countFieldsMatchedToPattern($pattern, $symbol) === 3
                    );

                return $carry;
            },
            false
        );

        if ($found === false) {
            return null;
        }

        return $game->players()[$symbol->value()];
    }

    /**
     * @param $pattern
     * @param Symbol $symbol
     * @return int
     */
    private function countFieldsMatchedToPattern($pattern, Symbol $symbol): int
    {
        $foundCount = 0;
        // Here were loop, but now I've changed to use native PHP array function
        // I'm not sure if it "improves" performance
        \array_walk(
            $this->board->contents(),
            function ($val, $i) use (&$foundCount, $symbol, $pattern) {
                /** @var \App\Core\Domain\Model\TicTacToe\ValueObject\Player $val */
                if (\is_null($val) === false && $val->symbol()->value() === $symbol->value() && $pattern[$i] == '#') {
                    $foundCount++;
                }
            }
        );

        return $foundCount;
    }
}
