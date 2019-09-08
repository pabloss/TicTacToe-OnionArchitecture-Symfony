<?php
declare(strict_types=1);

namespace App\AppCore\DomainServices\TurnControl\Validation;

use App\AppCore\DomainServices\TurnControl\Params;

/**
 * Interface ValidationInterface
 * @package App\AppCore\DomainServices\TurnControl\Validation
 */
interface ValidationInterface
{
    /**
     * @return int
     */
    public function errorCode(): int;

    /**
     * @param Params $params
     * @return bool
     */
    public function validate(Params $params): bool;
}
