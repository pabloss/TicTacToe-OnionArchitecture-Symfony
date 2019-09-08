<?php
declare(strict_types=1);

namespace App\AppCore\DomainModel\Game\AI;

use App\AppCore\DomainModel\Game\Board\Tile;
use App\AppCore\DomainModel\Game\Exception\OutOfLegalSizeException;
use App\AppCore\DomainModel\Game\Game as TicTacToe;

/**
 * Class AI
 * @package App\AppCore\DomainModel\Game\AI
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
     * @throws OutOfLegalSizeException
     */
    public function takeRandomFreeTile(): Tile
    {
        return $this->tileFromIndex(
            $this->chooseRandomIndex(
                $this->takeFreeTileIndexes($this->game->board()->contents())
            )
        );
    }

    /**
     * @param $randomIndex
     * @return \App\AppCore\DomainModel\Game\Board\Tile
     * @throws OutOfLegalSizeException
     */
    private function tileFromIndex($randomIndex): Tile
    {
        return new Tile($this->calculateRow($randomIndex), $this->calculateColumn($randomIndex));
    }


    /**
     * @param $randomIndex
     * @return int
     */
    private function calculateRow($randomIndex): int
    {
        return intval(floor($randomIndex / 3.0));
    }

    /**
     * @param $randomIndex
     * @return int
     */
    private function calculateColumn($randomIndex): int
    {
        return $randomIndex % 3;
    }

    /**
     * @param array $freeTileIndexes
     * @return int
     */
    private function chooseRandomIndex(array $freeTileIndexes): int
    {
        return $freeTileIndexes[rand(0, count($freeTileIndexes) - 1)];
    }


    /**
     * @param array $board
     * @return array
     */
    private function takeFreeTileIndexes(array &$board): array
    {
        $freeTileIndexes = [];
        array_walk(
            $board,
            function ($value, $key) use (&$freeTileIndexes) {
                if (null === $value) {
                    $freeTileIndexes[] = $key;
                }
            }
        );

        return $freeTileIndexes;
    }
}
