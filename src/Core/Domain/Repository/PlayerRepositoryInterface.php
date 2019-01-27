<?php
declare(strict_types=1);

namespace App\Core\Domain\Repository;

use App\Core\Domain\Model\TicTacToe\Game\Game as GameVO;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player as PlayerVO;

interface PlayerRepositoryInterface
{
    public function savePlayer(PlayerVO $player, GameVO $game);

    public function getPlayer(PlayerVO $player): PlayerVO;
}
