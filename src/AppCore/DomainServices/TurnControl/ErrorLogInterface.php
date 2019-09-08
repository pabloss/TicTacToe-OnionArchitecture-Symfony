<?php
declare(strict_types=1);

namespace App\AppCore\DomainServices\TurnControl;

use App\AppCore\DomainModel\Game\GameInterface;

/**
 * Interface ErrorLogInterface
 * @package App\AppCore\DomainServices\TurnControl
 */
interface ErrorLogInterface
{
    /**
     * @param int $error
     * @param GameInterface $game
     * @return mixed
     */
    public function addError(int $error, GameInterface $game);
}
