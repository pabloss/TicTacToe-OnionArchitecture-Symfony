<?php
declare(strict_types=1);

namespace App\Core\Domain\Repository;

use App\Core\Domain\Model\TicTacToe\Game\Game as GameVO;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player as PlayerVO;

/**
 * Interface PlayerRepositoryInterface
 * @package App\Core\Domain\Repository
 */
interface PlayerRepositoryInterface
{
    /**
     * @param PlayerVO $player
     * @param GameVO $game
     * @return mixed
     */
    public function savePlayer(PlayerVO $player, GameVO $game);

    /**
     * @param PlayerVO $player
     * @return PlayerVO
     */
    public function getPlayer(PlayerVO $player): PlayerVO;
}
