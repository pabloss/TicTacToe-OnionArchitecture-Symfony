<?php
declare(strict_types=1);

namespace App\Tests\Stubs\Event\Framework;

use App\Core\Application\Event\Event as CoreEvent;
use App\Core\Domain\Event\EventInterface;
use App\Core\Domain\Event\Params\ParamsInterface;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

/**
 * Class Event
 * @package App\Tests\Stubs\Event\Framework
 */
class Event extends SymfonyEvent implements EventInterface
{
    use CoreEvent { __construct as construct; }

    const NAME = '';

    /** @var string */
    private $name;

    /** @var ParamsInterface */
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
