<?php
declare(strict_types=1);

namespace App\Core\Domain\Service\TurnControl\Validation;

use App\Core\Domain\Service\TurnControl\AccessControl;
use App\Core\Domain\Service\TurnControl\ErrorLog;
use App\Core\Domain\Service\TurnControl\Params;

/**
 * Class PlayerShouldBeRegisteredValidation
 * @package App\Core\Domain\Service\TurnControl\Validation
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
