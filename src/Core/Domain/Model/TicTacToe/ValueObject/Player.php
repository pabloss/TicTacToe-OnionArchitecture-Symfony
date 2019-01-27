<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\ValueObject;

use App\Core\Domain\Event\EventManager;
use App\Core\Domain\Event\TileTakenEvent;
use App\Core\Domain\Model\TicTacToe\Game\Game;

class Player implements ValueObjectInterface
{
    /** @var Symbol */
    private $symbol;

    /** @var EventManager */
    private $eventManger;

    /** @var string */
    private $uuid;

    /**
     * Player constructor.
     * @param Symbol $symbol
     */
    public function __construct(Symbol $symbol)
    {
        $this->symbol = $symbol;
        $this->eventManger = EventManager::getInstance();
        $this->uuid = \uniqid();
    }

    public function symbol(): Symbol
    {
        return $this->symbol;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function takeTile(Tile $tile, Game $game): Tile
    {
        $this->eventManger->trigger(TileTakenEvent::NAME, [$this, $tile, $game]);

        return $tile;
    }
}
