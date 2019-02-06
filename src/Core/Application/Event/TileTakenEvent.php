<?php
declare(strict_types=1);

namespace App\Core\Application\Event;

use App\Core\Domain\Event\Params\ParamsInterface;

/**
 * Class TileTakenEvent
 * @package App\Core\Domain\Event
 */
class TileTakenEvent extends \App\Core\Application\Event\Event
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
