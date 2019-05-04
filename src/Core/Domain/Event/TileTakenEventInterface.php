<?php
declare(strict_types=1);

namespace App\Core\Domain\Event;

/**
 * Interface TileTakenEventInterface
 * @package App\Core\Domain\Event
 */
interface TileTakenEventInterface extends EventInterface
{
    const NAME = 'tile.taken';
}
