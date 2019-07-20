<?php
declare(strict_types=1);

namespace App\Core\Domain\Service\TurnControl;

use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;

/**
 * Class AccessControl
 * @package App\Core\Application\Validation
 */
class AccessControl
{
    /** @var PlayerRegistry */
    private static $registry;

    public static function loadRegistry(PlayerRegistry $registry)
    {
        self::$registry = $registry;
    }

    /**
     * @param Player $player
     * @param Game $game
     * @return bool
     */
    public static function isPlayerAllowed(Player $player, Game $game): bool
    {
        /** @var Player $internalPlayer */
        foreach (self::$registry->players($game) as $playerUuid) {
            if ($playerUuid === $player->uuid()) {
                return true;
            }
        }

        return false;
    }
}
