<?php
declare(strict_types=1);

namespace App\AppCore\DomainServices\TurnControl\Validation;

use App\AppCore\DomainServices\TurnControl\ErrorLog;
use App\AppCore\DomainServices\TurnControl\Params;

/**
 * Class PreviousPlayerShouldBeDifferentThanActualValidation
 * @package App\AppCore\DomainServices\TurnControl\Validation
 */
class PreviousPlayerShouldBeDifferentThanActualValidation implements ValidationInterface
{
    /**
     * @return int
     */
    public function errorCode(): int
    {
        return ErrorLog::DUPLICATED_TURNS_ERROR;
    }

    /**
     * @param Params $params
     * @return bool
     */
    public function validate(Params $params): bool
    {
        return !self::didPlayerPlayMovePreviously($params);
    }

    /**
     * @param Params $params
     * @return bool
     */
    private static function didPlayerPlayMovePreviously(Params $params): bool
    {
        return
            !empty($params->history()->lastItemPlayerSymbolValue($params->game())) &&
            $params->player()->symbolValue() === $params->history()->lastItemPlayerSymbolValue($params->game());
    }

}
