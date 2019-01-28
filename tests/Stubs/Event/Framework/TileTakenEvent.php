<?php
declare(strict_types=1);

namespace App\Tests\Stubs\Event\Framework;


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
