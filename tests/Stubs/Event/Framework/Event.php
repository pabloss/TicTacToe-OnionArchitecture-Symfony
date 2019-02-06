<?php
declare(strict_types=1);

namespace App\Tests\Stubs\Event\Framework;

use App\Core\Domain\Event\EventInterface;
use App\Core\Domain\Event\Params\ParamsInterface;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

/**
 * Class Event
 * @package App\Tests\Stubs\Event\Framework
 */
class Event extends SymfonyEvent implements EventInterface
{
    const NAME = '';

    /** @var string */
    private $name;

    /** @var ParamsInterface */
    private $params;

    /**
     * Event constructor.
     * @param string $name
     * @param ParamsInterface $params
     */
    public function __construct(string $name, ParamsInterface $params = null)
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
     * @return ParamsInterface|null
     */
    public function getParams(): ?ParamsInterface
    {
        return $this->params;
    }


}
