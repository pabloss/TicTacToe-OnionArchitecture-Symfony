<?php
declare(strict_types=1);

namespace App\Presentation\Web\Pub\Event;

use App\Core\Application\Event\Event as CoreEvent;
use App\Core\Domain\Event\EventInterface;
use App\Core\Domain\Event\Params\ParamsInterface;
use App\Core\Domain\Event\TileTakenEventInterface;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

/**
 * Class Event
 * @package App\Presentation\Web\Pub\Event
 */
class Event extends SymfonyEvent implements EventInterface, TileTakenEventInterface
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
