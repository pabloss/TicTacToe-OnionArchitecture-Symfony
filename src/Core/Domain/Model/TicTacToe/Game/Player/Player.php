<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\Game\Player;

/**
 * Class Player
 * @package App\Core\Domain\Model\TicTacToe\ValueObject
 */
class Player
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
     * @return string
     */
    public function symbolValue(): string
    {
        return $this->symbol()->value();
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
    public function uuid(): string
    {
        return $this->uuid;
    }
}
