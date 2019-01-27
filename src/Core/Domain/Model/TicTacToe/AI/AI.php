<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\AI;

use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;


/**
 * Class AI
 * @package App\Core\Domain\Model\TicTacToe\AI
 */
class AI
{
    /**
     * @var TicTacToe
     */
    private $game;

    /**
     * AI constructor.
     * @param TicTacToe $game
     */
    public function __construct(TicTacToe $game)
    {
        $this->game = $game;
    }


    /**
     * @return Tile
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\OutOfLegalSizeException
     */
    public function takeRandomFreeTile()
    {
        $board = $this->game->board();
        $freeTileIndexes = $this->takeFreeTileIndexes($board->contents());
        $randomIndex = $this->chooseRandomIndex($freeTileIndexes);
        list($column, $row) = $this->coordinatesFromIndex($randomIndex);

        return new Tile($row, $column);
    }

    /**
     * @param array $board
     * @return array
     */
    private function takeFreeTileIndexes(array $board)
    {
        $freeTileIndexes = [];
        \array_walk(
            $board,
            function ($value, $key) use (&$freeTileIndexes) {
                if (\is_null($value)) {
                    $freeTileIndexes[] = $key;
                }
            }
        );

        return $freeTileIndexes;
    }

    /**
     * @param array $freeTileIndexes
     * @return mixed
     */
    private function chooseRandomIndex(array $freeTileIndexes)
    {
        $arrayLength = \count($freeTileIndexes);

        return $freeTileIndexes[\rand(0, $arrayLength - 1)];
    }

    /**
     * @param $randomIndex
     * @return array
     */
    private function coordinatesFromIndex($randomIndex): array
    {
        $column = $randomIndex % 3;
        $row = \intval(\floor($randomIndex / 3.0));

        return array($column, $row);
    }
}
