<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\ValueObject;

use App\Core\Domain\Event\EventManagerInterface;
use App\Core\Domain\Event\TileTakenEventInterface;
use App\Core\Domain\Model\TicTacToe\Game\Game;

/**
 * Class Player
 * @package App\Core\Domain\Model\TicTacToe\ValueObject
 */
class Player implements ValueObjectInterface
{
    /** @var Symbol */
    private $symbol;

    /** @var EventManagerInterface */
    private $eventManger;

    /** @var string */
    private $uuid;

    /**
     * Player constructor.
     * @param Symbol $symbol
     * @param EventManagerInterface $eventManager
     */
    public function __construct(Symbol $symbol, EventManagerInterface $eventManager)
    {
        $this->symbol = $symbol;
        $this->eventManger = $eventManager;
        $this->uuid = \uniqid();
    }

    /**
     * @return Symbol
     */
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

    /**
     * @param Tile $tile
     * @param Game $game
     * @return Tile
     */
    public function takeTile(Tile $tile, Game $game): Tile
    {
        $this->eventManger->trigger(TileTakenEventInterface::NAME, [$this, $tile, $game]);

        return $tile;
    }
}
