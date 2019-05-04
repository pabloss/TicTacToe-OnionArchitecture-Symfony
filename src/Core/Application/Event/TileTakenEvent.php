<?php
declare(strict_types=1);

namespace App\Core\Application\Event;

use App\Core\Domain\Event\EventInterface;
use App\Core\Domain\Event\Params\ParamsInterface;

/**
 * Class TileTakenEvent
 * @package App\Core\Domain\Event
 */
class TileTakenEvent implements EventInterface
{
    use Event { __construct as construct; }

    const NAME = 'tile.taken';

    /**
     * TileTakenEvent constructor.
     * @param ParamsInterface $params
     */
    public function __construct(ParamsInterface $params)
    {
        $this->construct(self::NAME, $params);
    }
}
