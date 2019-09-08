<?php
declare(strict_types=1);

namespace App\AppCore\DomainModel\Game\Board;

use App\AppCore\DomainModel\Game\Exception\OutOfLegalSizeException;

/**
 * Class Tile
 * @package App\Core\Domain\Model\TicTacToe\ValueObject
 */
class Tile implements TileInterface
{
    const POSITION_UPPER_LIMIT = 3;
    /**
     * @var int
     */
    private $row;
    /**
     * @var int
     */
    private $column;

    /**
     * Tile constructor.
     * @param int $row
     * @param int $column
     * @throws OutOfLegalSizeException
     */
    public function __construct(int $row, int $column)
    {
        if ($row < 0 || $row >= self::POSITION_UPPER_LIMIT) {
            throw new OutOfLegalSizeException();
        }

        if ($column < 0 || $column >= self::POSITION_UPPER_LIMIT) {
            throw new OutOfLegalSizeException();
        }
        $this->row = $row;
        $this->column = $column;
    }

    /**
     * @return int
     */
    public function row(): int
    {
        return $this->row;
    }

    /**
     * @return int
     */
    public function column(): int
    {
        return $this->column;
    }
}
