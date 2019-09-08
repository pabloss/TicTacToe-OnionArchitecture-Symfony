<?php
declare(strict_types=1);

namespace App\AppCore\DomainModel\Game;

use App\AppCore\DomainModel\Game\Board\Board;

/**
 * Interface GameInterface
 * @package App\AppCore\DomainModel\Game
 */
interface GameInterface
{
    /**
     * @return string
     */
    public function uuid(): string;

    /**
     * @return Board
     */
    public function board(): Board;
}
