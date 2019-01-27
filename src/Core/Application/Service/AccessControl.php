<?php
declare(strict_types=1);

namespace App\Core\Application\Service;

use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player;

/**
 * Class AccessControl
 * @package App\Core\Application\Service
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
            if ($internalPlayer->getUuid() === $player->getUuid()) {
                return true;
            }
        }

        return false;
    }
}
