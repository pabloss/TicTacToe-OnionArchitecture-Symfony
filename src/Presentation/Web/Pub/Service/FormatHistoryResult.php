<?php
declare(strict_types=1);

namespace App\Presentation\Web\Pub\Service;

use App\Entity\History;
use App\Repository\HistoryRepository;

class FormatHistoryResult
{
    /** @var HistoryRepository */
    private $historyRepository;

    /**
     * FormatHistoryResult constructor.
     * @param HistoryRepository $historyRepository
     */
    public function __construct(HistoryRepository $historyRepository)
    {
        $this->historyRepository = $historyRepository;
    }

    /**
     * @return string[]
     */
    public function format(): array
    {
        $expectedResult = [];
        /** @var History $history */
        foreach ($this->historyRepository->findBy([], ['createdAt' => 'DESC']) as $history){
            $expectedResult[$history->getTile()[0]*3+$history->getTile()[1]] = $history->getPlayerSymbol();
        }
        return $expectedResult;
    }
}
