<?php
declare(strict_types=1);

namespace App\Presentation\Web\Pub\Event;

use App\Core\Domain\Event\EventInterface;
use App\Core\Domain\Event\Params\Params;
use App\Core\Domain\Event\Params\ParamsInterface;
use App\Core\Domain\Event\TileTakenEventInterface;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

class Event extends SymfonyEvent implements EventInterface, TileTakenEventInterface
{
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
        $this->name = $name;
        $this->params = $params;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParams(): ?ParamsInterface
    {
        return $this->params;
    }
}
