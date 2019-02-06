<?php

namespace App\Core\Application\Event\EventSubscriber;

use App\Core\Application\Event\TileTakenEvent;
use App\Core\Application\Service\AccessControl;
use App\Core\Domain\Event\EventInterface;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;

/**
 * Class TakeTileEventSubscriber
 * @package App\Core\Application\EventSubscriber
 */
class TakeTileEventSubscriber implements EventSubscriberInterface
{
    public static $counter = 0;
    /**
     * @param EventInterface $event
     * @return Tile
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
     */
    public static function onTakenTile(EventInterface $event)
    {
        /** @var $game Game */
        $params = $event->getParams();
        $player = $params->player();
        $tile = $params->tile();
        $game = $params->game();
        if (false === AccessControl::isPlayerAllowed($player, $game)) {
            $game->addError(Game::PLAYER_IS_NOT_ALLOWED, $player);
        }
        if (
            empty($game->history()->getLastTurn($game)) &&
            $player->symbol() != $game->history()->getStartingPlayerSymbol()
        ) {

            $game->addError(Game::GAME_STARTED_BY_PLAYER0_ERROR, $player);
        }
        if(!empty($game->history()->getLastTurn($game)) && $player->symbol()->value() === $game->history()->getLastTurn($game)->player()->symbol()->value()){
            $game->addError(Game::DUPLICATED_TURNS_ERROR, $player);
        }
        if ($game->errors() === Game::OK) {
            ++self::$counter;
            $game->board()->mark($tile, $player);

            $game->history()->saveTurn($player, $tile, $game);
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
