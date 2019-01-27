<?php

namespace App\Repository;

use App\Entity\History as HistoryEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Core\Domain\Model\TicTacToe\Game\History as HistoryVO;

/**
 * @method HistoryEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoryEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoryEntity[]    findAll()
 * @method HistoryEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepository
{
    /**
     * HistoryRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HistoryEntity::class);
    }

    /**
     * @param HistoryVO $history
     * @return HistoryEntity
     */
    public function findByVO(HistoryVO $history): HistoryEntity
    {
        return $this->findOneBy(['valueObject' => $history]);
    }
}
