<?php
declare(strict_types=1);

namespace App\Tests\Stubs\Event;

use App\Core\Domain\Event\Params\ParamsInterface;

/**
 * Class TileTakenEvent
 * @package App\Core\Domain\Event
 */
class TileTakenEvent extends Event
{
    const NAME = 'tile.taken';

    /**
     * TileTakenEvent constructor.
     * @param ParamsInterface $params
     */
    public function __construct(ParamsInterface $params)
    {
        parent::__construct(self::NAME, $params);
    }
}
