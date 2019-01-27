<?php
declare(strict_types=1);

namespace App\Entity;

/**
 * Interface EntityInterface
 * @package App\Entity
 */
interface EntityInterface
{
    /**
     * @return object
     */
    public function getValueObject(): object;

    /**
     * @param object $valueObject
     * @return EntityInterface
     */
    public function setValueObject(object $valueObject): self;
}
