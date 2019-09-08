<?php
declare(strict_types=1);

namespace App\AppCore\ApplicationServices;

use App\AppCore\DomainModel\Game\Board\Board;
use App\AppCore\DomainModel\Game\Exception\NotAllowedSymbolValue;
use App\AppCore\DomainModel\Game\GameInterface;
use App\AppCore\DomainModel\Game\Player\PlayerInterface;
use App\AppCore\DomainModel\Game\Player\Symbol;

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
     * @param GameInterface $game
     * @return PlayerInterface|null
     * @throws NotAllowedSymbolValue
     */
    public function winner(GameInterface $game): ?PlayerInterface
    {
        return
            $this->findWinnerByBoardPatterns(new Symbol(Symbol::PLAYER_X_SYMBOL), $game->board()) ??
            $this->findWinnerByBoardPatterns(new Symbol(Symbol::PLAYER_0_SYMBOL), $game->board());
    }

    /**
     * @param Symbol $symbol
     * @param Board $board
     * @return PlayerInterface|null
     */
    private function findWinnerByBoardPatterns(Symbol $symbol, Board $board): ?PlayerInterface
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
     * @return PlayerInterface|null
     */
    private function findPlayerByPatternAndSymbol($pattern, Board $board, Symbol $symbol): ?PlayerInterface
    {
        // todo: too complex to make small refactor
        $foundCount = 0;
        $foundPlayer = null;
        // Here were loop, but now I've changed to use native PHP array function
        // I'm not sure if it "improves" performance
        array_walk(
            $board->contents(),
            function ($player, $i) use (&$foundPlayer, &$foundCount, $symbol, $pattern) {
                /** @var PlayerInterface $player */
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
