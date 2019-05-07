<?php
declare(strict_types=1);

namespace App\Core\Application\Validation;

use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player;

class TurnControl
{
    /**
     * @param Player $player
     * @param Game $game
     * @param HistoryInterface $history
     * @throws NotAllowedSymbolValue
     */
    public static function validateTurn(Player $player, Game $game, HistoryInterface $history): void
    {
        if (false === AccessControl::isPlayerAllowed($player, $game)) {
            $game->addError(Game::PLAYER_IS_NOT_ALLOWED, $player);
        }
        if (self::isGameNotStartedByCorrectPlayer($player, $game, $history)) {
            $game->addError(Game::GAME_STARTED_BY_PLAYER0_ERROR, $player);
        }
        if (self::didPlayerPlayMovePreviously($player, $game, $history)) {
            $game->addError(Game::DUPLICATED_TURNS_ERROR, $player);
        }
    }

    /**
     * @param Player $player
     * @param Game $game
     * @param HistoryInterface $history
     * @return bool
     */
    private static function isGameNotStartedByCorrectPlayer(Player $player, Game $game, HistoryInterface $history): bool
    {
        return empty($history->lastItem($game)) && $player->symbolValue() !== $history->getStartingPlayerSymbolValue();
    }

    /**
     * @param Player $player
     * @param Game $game
     * @param HistoryInterface $history
     * @return bool
     */
    private static function didPlayerPlayMovePreviously(Player $player, Game $game, HistoryInterface $history): bool
    {
        return !empty($history->lastItem($game)) && $player->symbolValue() === $history->lastItemPlayerSymbolValue($game);
    }
}
