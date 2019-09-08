<?php
declare(strict_types=1);

namespace App\AppCore\DomainModel\Game\Player;

/**
 * Interface PlayerInterface
 * @package App\AppCore\DomainModel\Game\Player
 */
interface PlayerInterface
{
    /**
     * @return string
     */
    public function symbolValue(): string;

    /**
     * @return string
     */
    public function uuid(): string;
}
