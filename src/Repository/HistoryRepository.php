<?php

namespace App\Repository;

use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Entity\History as HistoryEntity;
use App\Entity\History;
use App\Tests\Stubs\History\History as HistoryVO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

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

    public function getLastByGame(Game $game): ?History
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('h')
            ->from(History::class, 'h')
            ->where('h.gameUuid = :gameUuid')
            ->orderBy('h.createdAt', 'DESC')
            ->setMaxResults(1)
            ->setParameter('gameUuid', $game->uuid())
        ;
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getLastByGameAndIndex(Game $game, int $index): ?History
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('h')
            ->from(History::class, 'h')
            ->where('h.gameUuid = :gameUuid')
            ->orderBy('h.createdAt', 'DESC')
            ->setMaxResults($index+1)
            ->setParameter('gameUuid', $game->uuid())
        ;
        $result = $qb->getQuery()->getResult();

        return \end($result);
    }



    /**
     * @param HistoryEntity $history
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(HistoryEntity $history)
    {
        $this->getEntityManager()->persist($history);
        $this->getEntityManager()->flush();
    }
}
