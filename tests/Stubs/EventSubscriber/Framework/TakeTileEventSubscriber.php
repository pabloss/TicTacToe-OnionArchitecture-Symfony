<?php
declare(strict_types=1);

namespace App\Tests\Stubs\EventSubscriber\Framework;

use App\Core\Application\EventSubscriber\EventSubscriberInterface;
use App\Core\Application\Service\AccessControl;
use App\Core\Domain\Event\EventInterface;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player;
use App\Tests\Stubs\Event\Framework\TileTakenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as SymfonyEventSubscriberInterface;

class TakeTileEventSubscriber implements SymfonyEventSubscriberInterface, EventSubscriberInterface
{
    public function getEventHandlers()
    {
        return self::getSubscribedEvents();
    }

    public static function getSubscribedEvents()
    {
        return [
            TileTakenEvent::NAME => 'onTakenTile',
        ];
    }

    public function onTakenTile(EventInterface $event)
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
}
