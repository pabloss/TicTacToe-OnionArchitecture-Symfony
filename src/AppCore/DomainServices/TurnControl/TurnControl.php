<?php
declare(strict_types=1);

namespace App\AppCore\DomainServices\TurnControl;

use App\AppCore\DomainServices\TurnControl\Validation\ValidationCollection;
use App\AppCore\DomainServices\TurnControl\Validation\ValidationInterface;

/**
 * Class TurnControl
 * @package App\Core\Application\Validation
 */
class TurnControl
{
    /** @var ValidationCollection */
    private $validationCollection;

    /** @var ErrorLogInterface */
    private $errorLog;

    /**
     * TurnControl constructor.
     * @param ValidationCollection $validationCollection
     * @param ErrorLogInterface $errorLog
     */
    public function __construct(ValidationCollection $validationCollection, ErrorLogInterface $errorLog)
    {
        $this->validationCollection = $validationCollection;
        $this->errorLog = $errorLog;
    }

    /**
     * @param Params $params
     */
    public function validateTurn(Params $params): void
    {
        /** @var ValidationInterface $item */
        foreach ($this->validationCollection as $item) {
            if(false === $item->validate($params)){
                $this->errorLog->addError($item->errorCode(), $params->game());
            }
        }
    }
}
