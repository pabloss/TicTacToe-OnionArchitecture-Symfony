<?php
declare(strict_types=1);

namespace App\AppCore\DomainServices\TurnControl;

use App\AppCore\DomainModel\Game\GameInterface;
use App\AppCore\DomainModel\Game\Player\PlayerInterface;

/**
 * Class PlayerRegistry
 * @package App\AppCore\DomainServices
 */
class PlayerRegistry
{
    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param PlayerInterface $player
     * @param GameInterface $game
     */
    public function registerPlayer(PlayerInterface $player, GameInterface $game): void
    {
        $this->registry[$game->uuid()][$player->symbolValue()] = $player->uuid();
    }

    /**
     * @param GameInterface $game
     * @return array
     */
    public function players(GameInterface $game): array
    {
        return $this->registry[$game->uuid()] ?? [];
    }
}
