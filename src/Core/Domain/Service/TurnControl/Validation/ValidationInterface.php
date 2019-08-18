<?php
declare(strict_types=1);

namespace App\Core\Domain\Service\TurnControl\Validation;

use App\Core\Domain\Service\TurnControl\Params;

interface ValidationInterface
{
    public function errorCode(): int;
    public function validate(Params $params): bool;
}
