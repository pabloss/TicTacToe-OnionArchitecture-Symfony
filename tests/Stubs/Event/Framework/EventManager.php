<?php
declare(strict_types=1);

namespace App\Tests\Stubs\Event\Framework;

use App\Core\Application\EventSubscriber\EventSubscriberInterface;
use App\Core\Domain\Event\EventManagerInterface;
use App\Tests\Stubs\Event\TileTakenEvent;
use App\Tests\Stubs\EventSubscriber\Framework\TakeTileEventSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
    public function __construct(EventDispatcherInterface $dispatcher, array $subscribers)
    {
        self::$dispatcher = $dispatcher;
        /** @var EventSubscriberInterface[] $subscribers */
        foreach ($subscribers as $implementation) {
            $handlers = (new $implementation)->getEventHandlers();
            foreach ($handlers as $eventName => $methodName) {
                $this->attach(
                    $eventName,
                    function (Event $e) use ($implementation, $methodName) {
                        if (\method_exists($implementation, $methodName)) {
                            $implementation::{$methodName}($e);
                        }
                    }
                );
            }
        }
    }

    public function attach(string $name, callable $callback): void
    {
        $methodName = TakeTileEventSubscriber::getSubscribedEvents()[TileTakenEvent::NAME];
        $this->events[$name][] = function () use ($methodName) {
            (new TakeTileEventSubscriber())->{$methodName}();
        };
    }

    public static function getInstance(array $subscribers): EventManagerInterface
    {
        if (null === self::$instance) {
            self::$instance = new self(self::$dispatcher, $subscribers);
        }

        return self::$instance;
    }

    public function trigger(string $name, array $params = array()): void
    {
        self::$dispatcher->dispatch($name, new Event($name, $params));
    }
}
