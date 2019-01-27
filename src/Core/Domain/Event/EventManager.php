<?php
declare(strict_types=1);

namespace App\Core\Domain\Event;

use App\Core\Application\EventSubscriber\TakeTileEventSubscriber;

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

    private function __construct()
    {
        $this->attach(
            TileTakenEvent::NAME,
            function (Event $e) {
                TakeTileEventSubscriber::onTakenTile($e->getParams()[0], $e->getParams()[1], $e->getParams()[2]);
            }
        );
    }

    /**
     * @param string $name
     * @param callable $callback
     */
    public function attach(string $name, callable $callback)
    {
        $this->events[$name][] = $callback;
    }

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
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
