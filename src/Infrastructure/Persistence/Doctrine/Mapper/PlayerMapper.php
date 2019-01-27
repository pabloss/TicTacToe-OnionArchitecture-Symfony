<?php

namespace App\Infrastructure\Persistence\Doctrine\Mapper;

use App\Core\Domain\Model\TicTacToe\ValueObject\Player as PlayerVO;
use App\Core\Domain\Model\TicTacToe\ValueObject\ValueObjectInterface;
use App\Entity\EntityInterface;
use App\Entity\Player as PlayerEntity;
use App\Repository\PlayerRepository;


/**
 * Class PlayerMapper
 * @package App\Infrastructure\Persistence\Doctrine\Mapper
 */
class PlayerMapper implements EntityMapperInterface
{
    /** @var PlayerRepository */
    private $playerRepository;

    /**
     * PlayerMapper constructor.
     * @param PlayerRepository $playerRepository
     */
    public function __construct(PlayerRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    /**
     * @param ValueObjectInterface $player
     * @return PlayerEntity
     */
    public function toEntity(ValueObjectInterface $player): EntityInterface
    {
        if (!($player instanceof PlayerVO)) {
            throw  new \InvalidArgumentException(
                \sprintf(
                    "\$%s should be %s instance, %s given.",
                    'player',
                    PlayerVO::class,
                    \get_class($player)
                )
            );
        }

        return $this->playerRepository->findByVO($player);
    }
}
