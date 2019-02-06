<?php
declare(strict_types=1);

namespace App\Presentation\Web\Pub\Event;

use App\Core\Application\Event\EventSubscriber\EventSubscriberInterface;
use App\Core\Domain\Event\EventManagerInterface;
use App\Core\Domain\Event\Params\ParamsInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Core\Application\Event\EventSubscriber\TakeTileEventSubscriber;

class EventManager implements EventManagerInterface
{
    /** @var EventDispatcher */
    private static $dispatcher;

    /** @var self */
    private static $instance;
    /** @var Event[] */
    private $events = array();

    /**
     * EventManager constructor.
     * @param EventDispatcherInterface $dispatcher
     * @param EventSubscriberInterface[] $subscribers
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        self::$dispatcher = $dispatcher;
    }

    public function attach(string $name, callable $callback): void
    {
        $methodName = 'onTakenTile';
        $this->events[$name][] = function () use ($methodName) {
            (new TakeTileEventSubscriber())->{$methodName}();
        };
    }

    public static function getInstance(): EventManagerInterface
    {
        if (null === self::$instance) {
            self::$instance = new self(self::$dispatcher);
        }

        return self::$instance;
    }

    public function trigger(string $name, ParamsInterface $params = null): void
    {
        self::$dispatcher->dispatch($name, new Event($name, $params));
    }
}
