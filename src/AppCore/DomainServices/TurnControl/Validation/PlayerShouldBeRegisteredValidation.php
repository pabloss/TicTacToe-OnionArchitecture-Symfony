<?php
declare(strict_types=1);

namespace App\AppCore\DomainServices\TurnControl\Validation;

use App\AppCore\DomainServices\TurnControl\AccessControl;
use App\AppCore\DomainServices\TurnControl\ErrorLog;
use App\AppCore\DomainServices\TurnControl\Params;

/**
 * Class PlayerShouldBeRegisteredValidation
 * @package App\AppCore\DomainServices\TurnControl\Validation
 */
class PlayerShouldBeRegisteredValidation implements ValidationInterface
{
    /**
     * @return int
     */
    public function errorCode(): int
    {
        return ErrorLog::PLAYER_IS_NOT_ALLOWED;
    }

    /**
     * @param Params $params
     * @return bool
     */
    public function validate(Params $params): bool
    {
        return  AccessControl::isPlayerAllowed($params->player(), $params->game());
    }

}
