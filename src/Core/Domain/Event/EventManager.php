<?php
declare(strict_types=1);

namespace App\Core\Domain\Event;

use App\Core\Application\EventSubscriber\EventSubscriberInterface;

/**
 * Example:
 *
 * $events = new EventManager;
 * $events->attach('do', function($e) {
 *      echo $e->getName() . "\n";
 *      print_r($e->getParams());
 * });
 *
 * $events->trigger('do', array('a', 'b', 'c'));
 *
 * Class EventManager
 * @package App\Core\Domain\Event
 */
class EventManager
{
    /** @var self */
    private static $instance;
    /** @var Event[] */
    private $events = array();

    /** @var EventSubscriberInterface[] */
    private function __construct(array $subscribers)
    {
        /** @var EventSubscriberInterface $implementation */
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

    /**
     * @param string $name
     * @param callable $callback
     */
    public function attach(string $name, callable $callback)
    {
        $this->events[$name][] = $callback;
    }

    /**
     * @param EventSubscriberInterface[] $subscribers
     * @return EventManager
     */
    public static function getInstance(array $subscribers): self
    {
        if (null === self::$instance) {
            self::$instance = new self($subscribers);
        }

        return self::$instance;
    }

    /**
     * @param string $name
     * @param array $params
     */
    public function trigger(string $name, $params = array())
    {
        foreach ($this->events[$name] as $event => $callback) {
            $e = new Event($name, $params);
            $callback($e);
        }
    }
}
