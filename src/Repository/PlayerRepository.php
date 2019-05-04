<?php

namespace App\Repository;

use App\Core\Domain\Model\TicTacToe\Game\Game as GameVO;
use App\Core\Domain\Model\TicTacToe\Game\History as HistoryVO;
use App\Core\Domain\Model\TicTacToe\Game\Player as PlayerVO;
use App\Core\Domain\Repository\PlayerRepositoryInterface;
use App\Entity\Game as GameEntity;
use App\Entity\History as HistoryEntity;
use App\Entity\Player as PlayerEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @method PlayerEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerEntity[]    findAll()
 * @method PlayerEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository implements PlayerRepositoryInterface
{
    /** @var GameRepository */
    private $gameRepository;

    /** @var ObjectManager */
    private $objectManager;

    /**
     * PlayerRepository constructor.
     * @param GameRepository $gameRepository
     * @param ObjectManager $objectManager
     */
    public function __construct(GameRepository $gameRepository, ObjectManager $objectManager)
    {
        $this->gameRepository = $gameRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @param
     * @return
     */
    public function findByVO(PlayerVO $player): PlayerEntity
    {
        return $this->findOneBy(['valueObject' => $player]);
    }
}
