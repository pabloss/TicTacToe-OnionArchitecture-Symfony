<?php
declare(strict_types=1);

namespace App\Tests\Stubs\Event\Framework;

use App\Core\Domain\Event\EventInterface;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

class Event extends SymfonyEvent implements EventInterface
{
    const NAME = '';

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

    public function getName(): string
    {
        return $this->name;
    }

    public function getParams(): array
    {
        return $this->params;
    }


}
