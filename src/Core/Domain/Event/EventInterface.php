<?php
declare(strict_types=1);

namespace App\Core\Domain\Event;

use App\Core\Domain\Event\Params\ParamsInterface;

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
     * @return ParamsInterface
     */
    public function getParams(): ?ParamsInterface;
}
