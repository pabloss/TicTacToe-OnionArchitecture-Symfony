<?php
declare(strict_types=1);

namespace App\Presentation\Web\Pub\Event;


use App\Core\Domain\Event\Params\ParamsInterface;

/**
 * Class TileTakenEvent
 * @package App\Presentation\Web\Pub\Event
 */
class TileTakenEvent extends Event
{
    /**
     * TileTakenEvent constructor.
     * @param ParamsInterface $params
     */
    public function __construct(ParamsInterface $params)
    {
        parent::__construct(self::NAME, $params);
    }

}
