<?php

namespace App\Repository;

use App\Core\Domain\Model\TicTacToe\Game\Game as GameVO;
use App\Core\Domain\Model\TicTacToe\Game\History as HistoryVO;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player as PlayerVO;
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

    public function savePlayer(PlayerVO $player, GameVO $game)
    {
        $playerEntity = new PlayerEntity();
        $playerEntity->setValueObject($player);

        if(empty($this->gameRepository->findByVO($game))){
            $gameEntity = new GameEntity();
            $gameEntity->setValueObject($game);

            $historyEntity = new HistoryEntity();
            $historyEntity->setValueObject(new HistoryVO());
            $gameEntity->setHistory($historyEntity);

            $playerEntity->setGame($gameEntity);

            $this->objectManager->persist($historyEntity);
            $this->objectManager->persist($gameEntity);

        } else{
            $gameEntity = $this->gameRepository->findByVO($game);
            $playerEntity->setGame($gameEntity);
            $playerEntity->setValueObject($player);
        }
        $this->objectManager->persist($playerEntity);
        $this->objectManager->flush();
    }

    public function getPlayer(PlayerVO $player): PlayerVO
    {
        return $this->findByVO($player)->getValueObject();
    }
}
