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
    const DUPLICATED_PLAYERS_ERROR = 1;

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
     * @param int $error
     * @param Game $game
     * @return bool
     */
    public function hasError(int $error, Game $game): bool
    {
        return !!($error & $this->errors[$game->uuid()]);
    }
}
