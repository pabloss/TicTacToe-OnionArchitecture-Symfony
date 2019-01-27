<?php

namespace App\Core\Application\EventSubscriber;

use App\Core\Application\Service\AccessControl;
use App\Core\Domain\Event\Event;
use App\Core\Domain\Event\TileTakenEvent;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;

/**
 * Class TakeTileEventSubscriber
 * @package App\Core\Application\EventSubscriber
 */
class TakeTileEventSubscriber implements EventSubscriberInterface
{
    /**
     * @param Event $event
     * @return Tile
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
     */
    public static function onTakenTile(Event $event)
    {
        /** @var $game Game */
        list($player, $tile, $game) = $event->getParams();
        if (false === AccessControl::isPlayerAllowed($player, $game)) {
            $game->addError(Game::PLAYER_IS_NOT_ALLOWED, $player);
        }
        /** @var $player Player */
        if (
            empty($game->history()->getLastTurn()) &&
            $player->symbol() != $game->history()->getStartingPlayerSymbol()
        ) {

            $game->addError(Game::GAME_STARTED_BY_PLAYER0_ERROR, $player);
        }

        if ($game->errors() === Game::OK) {
            $game->history()->saveLastTurn($player, $game);

            $game->board()->mark($tile, $player);

            $game->history()->saveTurnToHistory($tile);
        }

        return $tile;
    }

    public function getEventHandlers()
    {
        return [
            TileTakenEvent::NAME => 'onTakenTile',
        ];
    }
}
