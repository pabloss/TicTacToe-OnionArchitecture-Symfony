<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapper;

use App\Entity\EntityInterface;
use App\Entity\History as HistoryEntity;
use App\Repository\HistoryRepository;

/**
 * Class HistoryMapper
 */
class HistoryMapper implements EntityMapperInterface
{
    /** @var HistoryRepository */
    private $historyRepository;

    /**
     * HistoryMapper constructor.
     * @param HistoryRepository $historyRepository
     */
    public function __construct(HistoryRepository $historyRepository)
    {
        $this->historyRepository = $historyRepository;
    }

    /**
     * @param array $valueObjects
     * @return HistoryEntity
     */
    public function toEntity(... $valueObjects): EntityInterface
    {
        return $this->historyRepository->findByVO(\func_get_arg(0));
    }
}
