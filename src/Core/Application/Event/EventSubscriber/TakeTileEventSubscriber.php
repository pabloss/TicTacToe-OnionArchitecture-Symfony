<?php

namespace App\Core\Application\Event\EventSubscriber;

use App\Core\Application\Event\TileTakenEvent;
use App\Core\Application\Validation\TurnControl;
use App\Core\Domain\Event\EventInterface;
use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
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
     * @throws NotAllowedSymbolValue
     */
    public static function onTakenTile(EventInterface $event)
    {
        /** @var $game Game */
        $params = $event->getParams();
        $player = $params->player();
        $tile = $params->tile();
        $game = $params->game();
        TurnControl::validateTurn($player, $game);
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
