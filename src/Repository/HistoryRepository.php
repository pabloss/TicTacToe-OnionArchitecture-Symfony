<?php

namespace App\Repository;

use App\AppCore\DomainModel\Game\Board\Tile;
use App\AppCore\DomainModel\Game\GameInterface;
use App\AppCore\DomainModel\Game\Player\Player;
use App\AppCore\DomainModel\Game\Player\Symbol;
use App\AppCore\DomainModel\History\HistoryContent;
use App\AppCore\DomainModel\History\HistoryItem;
use App\AppCore\DomainModel\History\HistoryRepositoryInterface;
use App\Entity\History as HistoryEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HistoryEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoryEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoryEntity[]    findAll()
 * @method HistoryEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepository implements HistoryRepositoryInterface
{
    /**
     * HistoryRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HistoryEntity::class);
    }

    public function getLastByGame(GameInterface $game): ?HistoryItem
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('h')
            ->from(HistoryEntity::class, 'h')
            ->where('h.gameUuid = :gameUuid')
            ->orderBy('h.createdAt', 'DESC')
            ->setMaxResults(1)
            ->setParameter('gameUuid', $game->uuid());
        return new HistoryItem(
            new Player(new Symbol($qb->getQuery()->getOneOrNullResult()->getPlayerSymbol()),
                $qb->getQuery()->getOneOrNullResult()->getPlayerUuid()),
            new Tile($qb->getQuery()->getOneOrNullResult()->getTile()[0],
                $qb->getQuery()->getOneOrNullResult()->getTile()[1]),
            $game
        );
    }

    /**
     * @param HistoryItem $historyItem
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(HistoryItem $historyItem): void
    {
        $entity = new HistoryEntity();
        $entity->setPlayerSymbol($historyItem->player()->symbolValue());
        $entity->setPlayerUuid($historyItem->player()->uuid());
        $entity->setTile($historyItem->getTileArray());
        $entity->setGameUuid($historyItem->game()->uuid());
        $entity->setCreatedAt((string)\time());

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function getByGame(GameInterface $game): ?HistoryContent
    {
        $result = new HistoryContent();
        /** @var HistoryEntity $historyEntity */
        foreach ($this->findBy(['gameUuid' => $game->uuid()], ['createdAt' => 'DESC']) as $historyEntity) {
            $result->append(
                new HistoryItem(
                    new Player(new Symbol($historyEntity->getPlayerSymbol()), $historyEntity->getPlayerUuid()),
                    new Tile($historyEntity->getTile()[0], $historyEntity->getTile()[1]),
                    $game
                )
            );
        }
        return $result;
    }

    public function cleanupRepository(): void
    {
        $qb = $this->createQueryBuilder('h');
        $qb->delete()->getQuery()->execute();
    }
}
