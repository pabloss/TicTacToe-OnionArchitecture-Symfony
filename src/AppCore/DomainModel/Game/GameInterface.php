<?php
declare(strict_types=1);

namespace App\AppCore\DomainModel\Game;

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
}
