<?php
declare(strict_types=1);

namespace App\AppCore\ApplicationServices;

use App\AppCore\DomainModel\Game\GameInterface;
use App\AppCore\DomainModel\History\HistoryItem;
use App\AppCore\DomainModel\History\HistoryRepositoryInterface;

class FormatHistoryResult
{
    /** @var HistoryRepositoryInterface */
    private $historyRepository;

    /**
     * FormatHistoryResult constructor.
     * @param HistoryRepositoryInterface $historyRepository
     */
    public function __construct(HistoryRepositoryInterface $historyRepository)
    {
        $this->historyRepository = $historyRepository;
    }

    /**
     * @param GameInterface $game
     * @return string[]
     */
    public function format(GameInterface $game): array
    {
        $expectedResult = [];
        /** @var HistoryItem $history */
        foreach ($this->historyRepository->getByGame($game) as $history){
            $expectedResult[$history->getTileArray()[0]*3+$history->getTileArray()[1]] = $history->player()->symbolValue();
        }
        return $expectedResult;
    }
}
