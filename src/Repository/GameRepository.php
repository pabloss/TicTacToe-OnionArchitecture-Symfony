<?php

namespace App\Repository;

use App\Core\Domain\Model\TicTacToe\Game\Game as GameVO;
use App\Entity\Game as GameEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GameEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameEntity[]    findAll()
 * @method GameEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    /**
     * GameRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GameEntity::class);
    }

    /**
     * @param GameVO $game
     * @return GameEntity
     */
    public function findByVO(GameVO $game): GameEntity
    {
        return $this->findOneBy(['valueObject' => $game]);
    }
}
