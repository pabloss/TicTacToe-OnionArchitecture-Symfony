<?php
declare(strict_types=1);

namespace App\Core\Domain\Service\TurnControl;

use App\Core\Domain\Model\TicTacToe\Game\GameInterface;

interface ErrorLogInterface
{
    public function addError(int $error, GameInterface $game);
}
