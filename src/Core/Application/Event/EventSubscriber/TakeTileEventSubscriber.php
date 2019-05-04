<?php

namespace App\Core\Application\Event\EventSubscriber;

use App\Core\Application\Event\TileTakenEvent;
use App\Core\Application\Validation\TurnControl;
use App\Core\Domain\Event\EventInterface;
use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;

/**
 * Class TakeTileEventSubscriber
 * @package App\Core\Application\EventSubscriber
 */
class TakeTileEventSubscriber implements EventSubscriberInterface
{
    public static $counter = 0;

    /** @var HistoryInterface */
    private static $history;

    /**
     * TakeTileEventSubscriber constructor.
     * @param HistoryInterface $history
     * @todo zamien na __construct, by nie trzeba było pamiętać o wywołaniu tej metody
     */
    public static function init(HistoryInterface $history)
    {
        self::$history = $history;
    }

    /**
     * @param EventInterface $event
     * @return Tile
     * @throws NotAllowedSymbolValue
     * @todo zamień na niestatyczną by nie trzeba było pamiętać o self::init
     */
    public static function onTakenTile(EventInterface $event)
    {
        /** @var $game Game */
        TurnControl::validateTurn($event->player(), $event->game(), $event->gameHistory());
        if ($event->gameErrors() === Game::OK) {
            ++self::$counter;
            // todo: dodaj oddzielny subscriber do markowania planszy: jeśli był mark to zrób event toSaveToHistory
            $event->gameBoard()->mark($event->tile(), $event->player());

            self::$history->saveTurn($event->player(), $event->tile(), $event->game());
        }

        return $event->tile();
    }

    public function getEventHandlers()
    {
        return [
            TileTakenEvent::NAME => 'onTakenTile',
        ];
    }
}
