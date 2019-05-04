<?php
declare(strict_types=1);

namespace App\Tests\Stubs\Event;

use App\Core\Application\Event\Event as CoreEvent;
use App\Core\Domain\Event\EventInterface;
use App\Core\Domain\Event\Params\ParamsInterface;

/**
 * Class Event
 * @package App\Core\Domain\Event
 */
class Event implements EventInterface
{
    use CoreEvent { __construct as construct; }

    /** @var string */
    private $name;
    /** @var array */
    private $params;

    /**
     * Event constructor.
     * @param string $name
     * @param ParamsInterface $params
     */
    public function __construct(string $name, ParamsInterface $params = null)
    {
        $this->construct($name, $params);
    }
}
