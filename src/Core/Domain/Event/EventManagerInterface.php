<?php
declare(strict_types=1);

namespace App\Core\Domain\Event;

use App\Core\Domain\Event\Params\ParamsInterface;

/**
 * Interface EventManagerInterface
 * @package App\Core\Domain\Event
 */
interface EventManagerInterface
{
    /**
     * @return EventManagerInterface
     */
    public static function getInstance(): self;

    /**
     * @param string $eventName
     * @param callable $callback
     */
    public function attach(string $eventName, callable $callback): void;

    /**
     * @param string $eventName
     * @param ParamsInterface|null $params
     */
    public function trigger(string $eventName, ParamsInterface $params = null): void;
}
