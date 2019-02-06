<?php
declare(strict_types=1);

namespace App\Tests\Stubs\Event;

use App\Core\Application\Event\Event;
use App\Core\Application\Event\EventSubscriber\EventSubscriberInterface;
use App\Core\Domain\Event\EventManagerInterface;
use App\Core\Domain\Event\Params\ParamsInterface;

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
     * @param string $name
     * @param callable $callback
     */
    public function attach(string $name, callable $callback): void
    {
        $this->events[$name][] = $callback;
    }

    public function detach(string $name): void
    {
        unset($this->events[$name]);
    }

    /**
     * @param \App\Core\Application\Event\EventSubscriber\EventSubscriberInterface[] $subscribers
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
     * @param string $name
     * @param ParamsInterface $params
     */
    public function trigger(string $name, ParamsInterface $params = null): void
    {
        foreach ($this->events[$name] as $event => $callback) {
            $e = new Event($name, $params);
            $callback($e);
        }
    }
}
