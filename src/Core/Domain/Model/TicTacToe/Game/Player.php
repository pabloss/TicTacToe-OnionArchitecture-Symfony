<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\Game;

use App\Core\Domain\Event\Params\Params;
use App\Core\Domain\Event\TileTakenEventInterface;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use App\Core\Domain\Model\TicTacToe\ValueObject\ValueObjectInterface;

/**
 * Class Player
 * @package App\Core\Domain\Model\TicTacToe\ValueObject
 */
class Player implements ValueObjectInterface
{
    /** @var Symbol */
    private $symbol;

    /** @var string */
    private $uuid;

    /**
     * Player constructor.
     * @param Symbol $symbol
     * @param string $uuid
     */
    public function __construct(Symbol $symbol, string $uuid)
    {
        $this->symbol = $symbol;
        $this->uuid = $uuid;
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
    public function symbolValue(): string
    {
        return $this->symbol()->value();
    }

    /**
     * @return string
     */
    public function uuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param Tile $tile
     * @param Game $game
     * @param HistoryInterface $history
     * @return void
     */
    public function takeTile(Tile $tile, Game $game, HistoryInterface $history): void
    {
        $game->eventManger()->trigger(TileTakenEventInterface::NAME, new Params($this, $tile, $game, $history));
    }
}
