<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapper;

use App\Core\Domain\Model\TicTacToe\Game\History as HistoryVO;
use App\Core\Domain\Model\TicTacToe\ValueObject\ValueObjectInterface;
use App\Entity\EntityInterface;
use App\Entity\History as HistoryEntity;
use App\Repository\HistoryRepository;


/**
 * Class HistoryMapper
 * @package App\Infrastructure\Persistence\Doctrine\Mapper
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
     * @param ValueObjectInterface $history
     * @return HistoryEntity
     */
    public function toEntity(ValueObjectInterface $history): EntityInterface
    {
        if (!($history instanceof HistoryVO)) {
            throw  new \InvalidArgumentException(
                \sprintf(
                    "\$%s should be %s instance, %s given.",
                    'history',
                    HistoryVO::class,
                    \get_class($history)
                )
            );
        }

        return $this->historyRepository->findByVO($history);
    }
}
