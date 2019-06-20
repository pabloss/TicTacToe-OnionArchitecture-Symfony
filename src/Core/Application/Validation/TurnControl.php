<?php
declare(strict_types=1);

namespace App\Core\Application\Validation;

use App\Core\Application\Errors\ErrorLog;
use App\Core\Application\Service\PlayerRegistry;
use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player;

/**
 * Class TurnControl
 * @package App\Core\Application\Validation
 */
class TurnControl
{
    /** @var ErrorLog */
    private $errorLog;

    /**
     * TurnControl constructor.
     * @param PlayerRegistry $registry
     * @param ErrorLog $errorLog
     */
    public function __construct(PlayerRegistry $registry, ErrorLog $errorLog)
    {
        AccessControl::loadRegistry($registry);
        $this->errorLog = $errorLog;
    }

    /**
     * @param Player $player
     * @param Game $game
     * @param HistoryInterface $history
     */
    public  function validateTurn(Player $player, Game $game, HistoryInterface $history): void
    {
        if (false === AccessControl::isPlayerAllowed($player, $game)) {
            $this->errorLog->addError(Game::PLAYER_IS_NOT_ALLOWED, $game);
        }
        if (self::isGameNotStartedByCorrectPlayer($player, $game, $history)) {
            $this->errorLog->addError(Game::GAME_STARTED_BY_PLAYER0_ERROR, $game);
        }
        if (self::didPlayerPlayMovePreviously($player, $game, $history)) {
            $this->errorLog->addError(Game::DUPLICATED_TURNS_ERROR, $game);
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
