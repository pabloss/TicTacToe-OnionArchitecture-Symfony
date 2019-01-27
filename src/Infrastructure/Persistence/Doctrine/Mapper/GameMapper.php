<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapper;

use App\Core\Domain\Model\TicTacToe\Game\Game as GameVO;
use App\Core\Domain\Model\TicTacToe\ValueObject\ValueObjectInterface;
use App\Entity\EntityInterface;
use App\Entity\Game as GameEntity;
use App\Repository\GameRepository;

/**
 * Class GameMapper
 * @package App\Infrastructure\Persistence\Doctrine\Mapper
 */
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
     * @param \App\Core\Domain\Model\TicTacToe\ValueObject\ValueObjectInterface $game
     * @return GameEntity
     */
    public function toEntity(ValueObjectInterface $game): EntityInterface
    {
        if(!($game instanceof GameVO)){
            throw  new \InvalidArgumentException(\sprintf(
                    "\$%s should be %s instance, %s given.",
                    'game', GameVO::class, \get_class($game))
            );
        }
        return $this->gameRepository->findByVO($game);
    }
}
