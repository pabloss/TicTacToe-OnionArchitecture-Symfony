<?php
declare(strict_types=1);

namespace App\Entity;

/**
 * Interface EntityInterface
 * @package App\Entity
 */
interface EntityInterface
{
    public function getValueObject(): object;
    public function setValueObject(object $valueObject): self;
}
