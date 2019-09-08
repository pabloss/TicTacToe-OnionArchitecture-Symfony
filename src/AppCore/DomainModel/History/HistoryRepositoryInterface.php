<?php
declare(strict_types=1);

namespace App\AppCore\DomainModel\History;

use App\AppCore\DomainModel\Game\Game;

/**
 * Interface HistoryRepositoryInterface
 * @package App\AppCore\DomainModel\History
 */
interface HistoryRepositoryInterface
{
    /**
     * @param Game $game
     * @return HistoryItem|null
     */
    public function getLastByGame(Game $game): ?HistoryItem;

    /**
     * @param HistoryItem $historyItem
     */
    public function save(HistoryItem $historyItem): void;
}
