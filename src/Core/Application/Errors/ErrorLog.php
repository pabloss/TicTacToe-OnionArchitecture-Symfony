<?php
declare(strict_types=1);

namespace App\Core\Application\Errors;

use App\Core\Domain\Model\TicTacToe\Game\Game;

/**
 * Class ErrorLog
 * @package App\Core\Application\Errors
 */
class ErrorLog
{
    const OK = 0;
    const DUPLICATED_PLAYERS_ERROR = 1;
    const DUPLICATED_TURNS_ERROR = 2;
    const GAME_STARTED_BY_PLAYER0_ERROR = 4;
    const PLAYER_IS_NOT_ALLOWED = 8;
    const NON_EXISTING_ERROR = 16;
    const ERRORS = [self::DUPLICATED_PLAYERS_ERROR, self::DUPLICATED_TURNS_ERROR, self::GAME_STARTED_BY_PLAYER0_ERROR, self::PLAYER_IS_NOT_ALLOWED, self::NON_EXISTING_ERROR];

    /**
     * @var int
     */
    private $errors = [];


    /**
     * @param Game $game
     * @return bool
     */
    public function noErrors(Game $game)
    {
        if (!isset($this->errors[$game->uuid()])) {
            $this->errors[$game->uuid()] = 0;
        }
        return $this->errors[$game->uuid()] === 0;
    }

    /**
     * @param int $error
     * @param Game $game
     */
    public function addError(int $error, Game $game)
    {
        if (!isset($this->errors[$game->uuid()])) {
            $this->errors[$game->uuid()] = 0;
        }
        $this->errors[$game->uuid()] |= $error;
    }

    /**
     * @param Game $game
     * @return int
     */
    public function errors(Game $game): int
    {
        return $this->errors[$game->uuid()];
    }

    /**
     * @param int $error
     * @param Game $game
     * @return bool
     */
    public function hasError(int $error, Game $game): bool
    {
        return !!($error & $this->errors[$game->uuid()]);
    }
}
