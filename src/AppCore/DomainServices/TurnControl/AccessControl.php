<?php
declare(strict_types=1);

namespace App\AppCore\DomainServices\TurnControl;

use App\AppCore\DomainModel\Game\GameInterface;
use App\AppCore\DomainModel\Game\Player\PlayerInterface;

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
     * @param PlayerInterface $player
     * @param GameInterface $game
     * @return bool
     */
    public static function isPlayerAllowed(PlayerInterface $player, GameInterface $game): bool
    {
        if(false === empty(self::$registry)){
            foreach (self::$registry->players($game) as $playerUuid) {
                if ($playerUuid === $player->uuid()) {
                    return true;
                }
            }
        }

        return false;
    }
}
