<?php
declare(strict_types=1);

namespace App\Core\Domain\Service\TurnControl;

use App\Core\Domain\Model\TicTacToe\Game\GameInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player\PlayerInterface;

/**
 * Class PlayerRegistry
 * @package App\Core\Domain\Service
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
