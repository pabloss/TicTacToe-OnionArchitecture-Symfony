<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapper;

use App\Core\Domain\Model\TicTacToe\ValueObject\ValueObjectInterface as GameValueObject;
use App\Entity\EntityInterface;

/**
 * Interface EntityMapperInterface
 * @package App\Infrastructure\Persistence\Doctrine\Mapper
 */
interface EntityMapperInterface
{
    /**
     * @param GameValueObject $valueObject
     * @return EntityInterface
     */
    public function toEntity(GameValueObject $valueObject): EntityInterface;
}
