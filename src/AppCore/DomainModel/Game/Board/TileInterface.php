<?php
declare(strict_types=1);

namespace App\AppCore\DomainModel\Game\Board;

/**
 * Interface TileInterface
 * @package App\AppCore\DomainModel\Game\Board
 */
interface TileInterface
{
    /**
     * @return int
     */
    public function row(): int;

    /**
     * @return int
     */
    public function column(): int;
}
