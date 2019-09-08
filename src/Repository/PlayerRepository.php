<?php

namespace App\Repository;

use App\Entity\Player as PlayerEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @method PlayerEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerEntity[]    findAll()
 * @method PlayerEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
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
}
