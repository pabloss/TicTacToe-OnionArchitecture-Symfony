<?php
declare(strict_types=1);

namespace App\Tests\Stubs\Event;

use App\Core\Domain\Event\EventInterface;

/**
 * Class Event
 * @package App\Core\Domain\Event
 */
class Event implements EventInterface
{
    /** @var string */
    private $name;
    /** @var array */
    private $params;

    /**
     * Event constructor.
     * @param string $name
     * @param array $params
     */
    public function __construct(string $name, $params = array())
    {
        $this->name = $name;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
