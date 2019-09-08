<?php
declare(strict_types=1);

namespace App\AppCore\DomainServices\TurnControl;

use App\AppCore\DomainModel\Game\GameInterface;

/**
 * Class ErrorLog
 * @package App\Core\Application\Errors
 */
class ErrorLog implements ErrorLogInterface
{
    const OK = 0;
    const DUPLICATED_PLAYERS_ERROR = 1;
    const DUPLICATED_TURNS_ERROR = 2;
    const GAME_STARTED_BY_PLAYER0_ERROR = 4;
    const PLAYER_IS_NOT_ALLOWED = 8;
    const NON_EXISTING_ERROR = 16;
    const DUPLICATED_TILE_ERROR = 32;
    const ERRORS = [
        self::DUPLICATED_PLAYERS_ERROR,
        self::DUPLICATED_TURNS_ERROR,
        self::GAME_STARTED_BY_PLAYER0_ERROR,
        self::PLAYER_IS_NOT_ALLOWED,
        self::NON_EXISTING_ERROR,
        self::DUPLICATED_TILE_ERROR,
    ];

    /**
     * @var int
     */
    private $errors = [];


    /**
     * @param GameInterface $game
     * @return bool
     */
    public function noErrors(GameInterface $game)
    {
        if (!isset($this->errors[$game->uuid()])) {
            $this->errors[$game->uuid()] = 0;
        }
        return $this->errors[$game->uuid()] === 0;
    }

    /**
     * @param int $error
     * @param GameInterface $game
     */
    public function addError(int $error, GameInterface $game)
    {
        if (!isset($this->errors[$game->uuid()])) {
            $this->errors[$game->uuid()] = 0;
        }
        $this->errors[$game->uuid()] |= $error;
    }

    /**
     * @param GameInterface $game
     * @return int
     */
    public function errors(GameInterface $game): int
    {
        return $this->errors[$game->uuid()] ?? self::OK;
    }

    /**
     * @param int $error
     * @param GameInterface $game
     * @return bool
     */
    public function hasError(int $error, GameInterface $game): bool
    {
        return !!($error & $this->errors[$game->uuid()]);
    }
}
