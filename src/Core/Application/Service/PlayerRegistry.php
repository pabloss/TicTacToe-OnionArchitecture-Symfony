<?php
declare(strict_types=1);

namespace App\Core\Application\Service;

use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player;

/**
 * Class PlayerRegistry
 * @package App\Core\Application\Service
 */
class PlayerRegistry
{
    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param Player $player
     * @param Game $game
     */
    public function registerPlayer(Player $player, Game $game): void
    {
        $this->registry[$game->uuid()][$player->symbolValue()] = $player->uuid();
    }

    /**
     * @param Game $game
     * @return array
     */
    public function players(Game $game): array
    {
        return $this->registry[$game->uuid()] ?? [];
    }
}
