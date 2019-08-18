<?php
declare(strict_types=1);

namespace App\Core\Domain\Service\TurnControl;

use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Service\History\HistoryInterface;
use App\Core\Domain\Service\TurnControl\Validation\ValidationCollection;
use App\Core\Domain\Service\TurnControl\Validation\ValidationInterface;

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
