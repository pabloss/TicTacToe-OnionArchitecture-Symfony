<?php
declare(strict_types=1);

namespace App\Presentation\Web\Pub\Event;

use App\Core\Application\Event\EventSubscriber\EventSubscriberInterface;
use App\Core\Domain\Event\TileTakenEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as SymfonyEventSubscriberInterface;

class TakeTileEventSubscriber extends \App\Core\Application\Event\EventSubscriber\TakeTileEventSubscriber implements SymfonyEventSubscriberInterface, EventSubscriberInterface
{
    public function getEventHandlers()
    {
        return self::getSubscribedEvents();
    }

    public static function getSubscribedEvents()
    {
        return [
            TileTakenEventInterface::NAME => 'onTakenTile',
        ];
    }
}
