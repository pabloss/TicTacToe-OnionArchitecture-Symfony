<?php
declare(strict_types=1);

namespace App\Core\Domain\Event;


/**
 * Class TileTakenEvent
 * @package App\Core\Domain\Event
 */
class TileTakenEvent extends Event
{
    const NAME = 'tile.taken';

    /**
     * TileTakenEvent constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        parent::__construct(self::NAME, $params);
    }
}
