<?php
declare(strict_types=1);

namespace App\Presentation\Web\Pub\Event;


class TileTakenEvent extends Event
{
    /**
     * TileTakenEvent constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        parent::__construct(self::NAME, $params);
    }
}
