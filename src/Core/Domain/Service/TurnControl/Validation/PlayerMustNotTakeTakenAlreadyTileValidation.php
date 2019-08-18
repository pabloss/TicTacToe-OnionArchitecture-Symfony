<?php
declare(strict_types=1);

namespace App\Core\Domain\Service\TurnControl\Validation;

use App\Core\Domain\Service\TurnControl\ErrorLog;
use App\Core\Domain\Service\TurnControl\Params;

/**
 * Class PlayerMustNotTakeTakenAlreadyTileValidation
 * @package App\Core\Domain\Service\TurnControl\Validation
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
