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
     * @param Player $player
     * @param Game $game
     * @return bool
     * @throws NotAllowedSymbolValue
     */
    public static function isPlayerAllowed(Player $player, Game $game): bool
    {
        /** @var Player $internalPlayer */
        foreach ($game->players() as $internalPlayer) {
            if ($internalPlayer->uuid() === $player->uuid()) {
                return true;
            }
        }

        return false;
    }
}
