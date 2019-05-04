<?php
declare(strict_types=1);

namespace App\Presentation\Web\Pub\Event;

use App\Core\Application\Event\EventSubscriber\EventSubscriberInterface;
use App\Core\Domain\Event\EventManagerInterface;
use App\Core\Domain\Event\Params\ParamsInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventManager implements EventManagerInterface
{
    /** @var EventDispatcher */
    private static $dispatcher;

    /** @var self */
    private static $instance;

    /**
     * EventManager constructor.
     * @param EventDispatcherInterface $dispatcher
     * @param EventSubscriberInterface[] $subscribers
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        self::$dispatcher = $dispatcher;
    }

    public function attach(string $eventName, callable $callback): void
    {
    }

    public static function getInstance(): EventManagerInterface
    {
        if (null === self::$instance) {
            self::$instance = new self(self::$dispatcher);
        }

        return self::$instance;
    }

    public function trigger(string $eventName, ParamsInterface $params = null): void
    {
        self::$dispatcher->dispatch($eventName, new Event($eventName, $params));
    }
}
