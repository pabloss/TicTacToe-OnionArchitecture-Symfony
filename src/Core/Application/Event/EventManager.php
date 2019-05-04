<?php
declare(strict_types=1);

namespace App\Core\Application\Event;

use App\Core\Application\Event\EventSubscriber\EventSubscriberInterface;
use App\Core\Domain\Event\EventManagerInterface;
use App\Core\Domain\Event\Params\ParamsInterface;
use App\Tests\Stubs\Event\Event;

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
class EventManager implements EventManagerInterface
{
    /** @var self */
    private static $instance;
    /** @var Event[] */
    private $events = array();


    /**
     * @param string $eventName
     * @param callable $callback
     */
    public function attach(string $eventName, callable $callback): void
    {
        $this->events[$eventName][] = $callback;
    }

    public function detach(string $name): void
    {
        unset($this->events[$name]);
    }

    /**
     * @param EventSubscriberInterface[] $subscribers
     * @return EventManager
     */
    public static function getInstance(): EventManagerInterface
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @param string $eventName
     * @param ParamsInterface $params
     */
    public function trigger(string $eventName, ParamsInterface $params = null): void
    {
        foreach ($this->events[$eventName] as $event => $callback) {
            $e = new Event($eventName, $params);
            $callback($e);
        }
    }
}
