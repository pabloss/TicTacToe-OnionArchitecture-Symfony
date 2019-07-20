<?php
declare(strict_types=1);

namespace App\Core\Application\Query;

use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Board\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use function array_reduce;
use function array_walk;
use function is_null;


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
class FindWinnerService
{
    const MATCHED_PATTERN_EXPECTED_FIELD_COUNT = 3;
    const MARKED_FIELD_GENERIC_SYMBOL = '#';
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
     * @param Game $game
     * @return Player|null
     * @throws NotAllowedSymbolValue
     */
    public function winner(Game $game): ?Player
    {
        return
            $this->findWinnerByBoardPatterns(new Symbol(Symbol::PLAYER_X_SYMBOL), $game->board()) ??
            $this->findWinnerByBoardPatterns(new Symbol(Symbol::PLAYER_0_SYMBOL), $game->board());
    }

    /**
     * @param Symbol $symbol
     * @param Board $board
     * @return Player|null
     */
    private function findWinnerByBoardPatterns(Symbol $symbol, Board $board): ?Player
    {
        return array_reduce(
            self::patterns,
            function ($carry, $pattern) use ($symbol, $board) {
                if (null === $carry) {
                    return $this->findPlayerByPatternAndSymbol($pattern, $board, $symbol);
                }
                return $carry;
            },
            null
        );
    }

    /**
     * @param $pattern
     * @param Board $board
     * @param Symbol $symbol
     * @return Player|null
     */
    private function findPlayerByPatternAndSymbol($pattern, Board $board, Symbol $symbol): ?Player
    {
        // todo: too complex to make small refactor
        $foundCount = 0;
        $foundPlayer = null;
        // Here were loop, but now I've changed to use native PHP array function
        // I'm not sure if it "improves" performance
        array_walk(
            $board->contents(),
            function ($player, $i) use (&$foundPlayer, &$foundCount, $symbol, $pattern) {
                /** @var Player $player */
                if (is_null($player) === false && $player->symbol()->value() === $symbol->value() && $pattern[$i] == self::MARKED_FIELD_GENERIC_SYMBOL) {
                    $foundCount++;
                    $foundPlayer = $player;
                }
            }
        );

        return ($foundCount === self::MATCHED_PATTERN_EXPECTED_FIELD_COUNT) ?
            $foundPlayer : null;
    }
}
