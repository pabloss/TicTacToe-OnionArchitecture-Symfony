<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapper;

use App\Core\Domain\Model\TicTacToe\ValueObject\ValueObjectInterface as GameValueObject;
use App\Entity\EntityInterface;

interface EntityMapperInterface
{
    public function toEntity(GameValueObject $valueObject): EntityInterface;
}
