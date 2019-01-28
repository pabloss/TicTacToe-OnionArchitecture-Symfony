<?php
declare(strict_types=1);

namespace App\Core\Domain\Event;

interface EventManagerInterface
{
    public static function getInstance(array $subscribers): self;

    public function attach(string $name, callable $callback): void;

    public function trigger(string $name, array $params = array()): void;
}
