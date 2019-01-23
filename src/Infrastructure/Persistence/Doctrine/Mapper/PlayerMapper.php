<?php

namespace App\Infrastructure\Persistence\Doctrine\Mapper;

use App\Entity\EntityInterface;
use App\Entity\Player as PlayerEntity;

/**
 * Class PlayerMapper
 */
class PlayerMapper implements EntityMapperInterface
{
    /** @var GameMapper */
    private $gameMapper;

    /**
     * PlayerMapper constructor.
     * @param GameMapper $gameMapper
     */
    public function __construct(GameMapper $gameMapper)
    {
        $this->gameMapper = $gameMapper;
    }

    /**
     * @param array $valueObjects
     * @return PlayerEntity
     */
    public function toEntity(... $valueObjects): EntityInterface
    {
        $playerVO = \func_get_arg(0);
        $gameVO = \func_get_arg(1);
        $gameEntity = $this->gameMapper->toEntity($gameVO);

        $playerEntity = new PlayerEntity();
        $playerEntity->setSymbol($playerVO->symbol()->value());
        $playerEntity->setValueObject($playerVO);
        $playerEntity->setGame($gameEntity);

        return $playerEntity;
    }
}
