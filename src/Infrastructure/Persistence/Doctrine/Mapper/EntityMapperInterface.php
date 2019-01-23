<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapper;

use App\Entity\EntityInterface;

interface EntityMapperInterface
{
    public function toEntity(... $valueObjects): EntityInterface;
}
