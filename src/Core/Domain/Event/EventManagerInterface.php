<?php
declare(strict_types=1);

namespace App\Core\Domain\Event;

use App\Core\Domain\Event\Params\ParamsInterface;

interface EventManagerInterface
{
    public static function getInstance(): self;

    public function attach(string $name, callable $callback): void;

    public function trigger(string $name, ParamsInterface $params = null): void;
}
