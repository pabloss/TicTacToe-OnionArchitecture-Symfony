<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\Game;

use App\Core\Application\Validation\AccessControl;
use App\Core\Domain\Event\EventManagerInterface;
use App\Core\Domain\Model\TicTacToe\Exception;
use App\Core\Domain\Service\FindWinner;
use App\Core\Domain\Service\PlayersFactory;
use App\Tests\Stubs\History\History;
use App\Core\Application\History\HistoryItem;

/**
 * Class Game
 * @package App\Core\Domain\Model\TicTacToe\Game
 */
class Game
{
    /**
     * @var Board
     */
    private $board;
    /**
     * @var History
     */
    private $history;
    /**
     * @var PlayersFactory
     */
    private $factory;
    /**
     * @var FindWinner
     */
    private $findWinner;

    /**
     * @var int
     */
    private $errors;
    /**
     * @var Player[]
     */
    private $players;

    /** @var string $uuid */
    private $uuid;

    /** @var EventManagerInterface $eventManager */
    private $eventManager;

    /**
     * Game constructor.
     * @param Board $board
     */
    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    /**
     * @return Player[]
     * @throws Exception\NotAllowedSymbolValue
     */
    public function players()
    {
        if (empty($this->players)) {
            $this->players = $this->factory->create();
        }

        return $this->players;
    }

    /**
     * @return Board
     */
    public function &board(): Board
    {
        return $this->board;
    }

    /**
     * @return Player|null
     * @throws Exception\NotAllowedSymbolValue
     */
    public function winner(): ?Player
    {
        return $this->findWinner->winner($this);
    }

    /**
     * @param int $error
     * @param Player $player
     * @throws Exception\NotAllowedSymbolValue
     */
    public function addError(int $error, Player $player)
    {
        if (false === AccessControl::isPlayerAllowed($player, $this)) {
            $this->errors |= self::PLAYER_IS_NOT_ALLOWED;
        }
        if (false === \in_array($error, self::ERRORS)) {
            $this->errors |= self::NON_EXISTING_ERROR;
        }
        $this->errors |= $error;

    }

    /**
     * @return int
     */
    public function errors()
    {
        return $this->errors;
    }

    public function uuid(): string
    {
        return '1';
//        return $this->uuid;
    }    

    /**
     * @return EventManagerInterface
     */
    public function eventManger(): EventManagerInterface
    {
        return  $this->eventManager;
    }
}
