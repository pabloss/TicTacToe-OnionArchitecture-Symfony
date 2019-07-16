<?php
declare(strict_types=1);

namespace App\Core\Application\Service;

use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;

/**
 * Class PlayersFactory
 */
class PlayersFactory
{
    /**
     * @return \App\Core\Domain\Model\TicTacToe\Game\Player\Player[]
     * @throws NotAllowedSymbolValue
     */
    public function create(): array
    {
        return $this->players(new Symbol(Symbol::PLAYER_X_SYMBOL), new Symbol(Symbol::PLAYER_0_SYMBOL));
    }

    /**
     * @param \App\Core\Domain\Model\TicTacToe\Game\Player\Symbol $symbolX
     * @param \App\Core\Domain\Model\TicTacToe\Game\Player\Symbol $symbol0
     * @return array
     */
    private function players(Symbol $symbolX, Symbol $symbol0)
    {
        return [
            $symbolX->value() => new Player($symbolX, \uniqid()),
            $symbol0->value() => new Player($symbol0, \uniqid()),
        ];
    }
}
