<?php
declare(strict_types=1);

namespace App\Core\Domain\Event;

/**
 * Interface EventInterface
 * @package App\Core\Domain\Event
 */
interface EventInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return array
     */
    public function getParams(): array;
}
