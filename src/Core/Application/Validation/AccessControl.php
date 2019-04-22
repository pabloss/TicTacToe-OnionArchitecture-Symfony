<?php
declare(strict_types=1);

namespace App\Core\Application\Validation;

use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player;

/**
 * Class AccessControl
 * @package App\Core\Application\Validation
 */
class AccessControl
{
    /**
     * @param \App\Core\Domain\Model\TicTacToe\Game\Player $player
     * @param Game $game
     * @return bool
     * @throws NotAllowedSymbolValue
     */
    public static function isPlayerAllowed(Player $player, Game $game): bool
    {
        /** @var \App\Core\Domain\Model\TicTacToe\Game\Player $internalPlayer */
        foreach ($game->players() as $internalPlayer) {
            if ($internalPlayer->getUuid() === $player->getUuid()) {
                return true;
            }
        }

        return false;
    }
}
