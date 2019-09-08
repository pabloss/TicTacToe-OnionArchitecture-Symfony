<?php
declare(strict_types=1);

namespace App\AppCore\DomainServices\TurnControl\Validation;

use App\AppCore\DomainServices\TurnControl\ErrorLog;
use App\AppCore\DomainServices\TurnControl\Params;

/**
 * Class PlayerMustNotTakeTakenAlreadyTileValidation
 * @package App\AppCore\DomainServices\TurnControl\Validation
 */
class PlayerMustNotTakeTakenAlreadyTileValidation implements ValidationInterface
{
    public function errorCode(): int
    {
        return ErrorLog::DUPLICATED_TILE_ERROR;
    }

    public function validate(Params $params): bool
    {
        return !self::isTileTakenPreviously($params);
    }

    private static function isTileTakenPreviously(Params $params): bool
    {
        return $params->history()->content($params->game())->hasTile($params->tile());
    }
}
