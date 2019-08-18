<?php
declare(strict_types=1);

namespace App\Core\Domain\Service\TurnControl\Validation;

use App\Core\Domain\Service\TurnControl\ErrorLog;
use App\Core\Domain\Service\TurnControl\Params;

/**
 * Class GameShouldStartWithCorrectPlayerValidation
 * @package App\Core\Domain\Service\TurnControl\Validation
 */
class GameShouldStartWithCorrectPlayerValidation implements ValidationInterface
{
    /**
     * @return int
     */
    public function errorCode(): int
    {
        return  ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR;
    }

    /**
     * @param Params $params
     * @return bool
     */
    public function validate(Params $params): bool
    {
        return !$this->isGameNotStartedByCorrectPlayer($params);
    }

    /**
     * @param Params $params
     * @return bool
     */
    private function isGameNotStartedByCorrectPlayer(Params $params): bool
    {
        return empty($params->history()->lastItem($params->game())) && $params->player()->symbolValue() !== $params->history()->getStartingPlayerSymbolValue();
    }

}
