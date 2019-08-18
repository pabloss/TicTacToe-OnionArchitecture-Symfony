<?php
declare(strict_types=1);

namespace App\Core\Domain\Service\TurnControl\Validation;

use App\Core\Domain\Service\TurnControl\ErrorLog;
use App\Core\Domain\Service\TurnControl\Params;

/**
 * Class PreviousPlayerShouldBeDifferentThanActualValidation
 * @package App\Core\Domain\Service\TurnControl\Validation
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
