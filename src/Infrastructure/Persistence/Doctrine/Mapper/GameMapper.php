<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapper;

use App\Entity\EntityInterface;
use App\Entity\Game as GameEntity;
use App\Repository\GameRepository;

class GameMapper implements EntityMapperInterface
{
    /** @var GameRepository */
    private $gameRepository;

    /**
     * GameMapper constructor.
     * @param GameRepository $gameRepository
     */
    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    /**
     * @param array $valueObjects
     * @return GameEntity
     */
    public function toEntity(... $valueObjects): EntityInterface
    {
        return $this->gameRepository->findByVO(\func_get_arg(0));
    }
}
