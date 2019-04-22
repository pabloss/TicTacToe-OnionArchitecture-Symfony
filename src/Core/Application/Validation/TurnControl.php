<?php
declare(strict_types=1);

namespace App\Core\Application\Validation;

use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player;

class TurnControl
{
    /**
     * @param Player $player
     * @param Game $game
     * @throws NotAllowedSymbolValue
     */
    public static function validateTurn(Player $player, Game $game): void
    {
        if (false === AccessControl::isPlayerAllowed($player, $game)) {
            $game->addError(Game::PLAYER_IS_NOT_ALLOWED, $player);
        }
        if (
            empty($game->history()->getLastTurn($game)) &&
            $player->symbol() != $game->history()->getStartingPlayerSymbol()
        ) {

            $game->addError(Game::GAME_STARTED_BY_PLAYER0_ERROR, $player);
        }
        if (!empty($game->history()->getLastTurn($game)) && $player->symbol()->value() === $game->history()->getLastTurn($game)->player()->symbol()->value()) {
            $game->addError(Game::DUPLICATED_TURNS_ERROR, $player);
        }
    }
}
