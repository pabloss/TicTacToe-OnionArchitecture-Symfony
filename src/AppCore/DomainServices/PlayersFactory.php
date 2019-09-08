<?php
declare(strict_types=1);

namespace App\AppCore\DomainServices;

use App\AppCore\DomainModel\Game\Exception\NotAllowedSymbolValue;
use App\AppCore\DomainModel\Game\Player\Player;
use App\AppCore\DomainModel\Game\Player\Symbol;

/**
 * Class PlayersFactory
 */
class PlayersFactory
{
    /**
     * @return Player[]
     * @throws NotAllowedSymbolValue
     */
    public function create(): array
    {
        return $this->players(new Symbol(Symbol::PLAYER_X_SYMBOL), new Symbol(Symbol::PLAYER_0_SYMBOL));
    }

    /**
     * @param Symbol $symbolX
     * @param Symbol $symbol0
     * @return array
     */
    private function players(Symbol $symbolX, Symbol $symbol0)
    {
        return [
            $symbolX->value() => new Player($symbolX, uniqid()),
            $symbol0->value() => new Player($symbol0, uniqid()),
        ];
    }
}
