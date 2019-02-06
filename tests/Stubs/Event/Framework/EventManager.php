<?php
declare(strict_types=1);

namespace App\Tests\Stubs\Event\Framework;

use App\Core\Application\EventSubscriber\EventSubscriberInterface;
use App\Core\Domain\Event\EventManagerInterface;
use App\Core\Domain\Event\Params\ParamsInterface;
use App\Tests\Stubs\Event\TileTakenEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Presentation\Web\Pub\Event\TakeTileEventSubscriber;

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
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        self::$dispatcher = $dispatcher;
        /** @var EventSubscriberInterface[] $subscribers */
    }

    public function attach(string $name, callable $callback): void
    {
        $methodName = TakeTileEventSubscriber::getSubscribedEvents()[TileTakenEvent::NAME];
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
