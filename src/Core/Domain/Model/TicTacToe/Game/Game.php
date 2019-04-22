<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\Game;

use App\Core\Application\Validation\AccessControl;
use App\Core\Domain\Event\EventManagerInterface;
use App\Core\Domain\Model\TicTacToe\Exception;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player;
use App\Core\Domain\Service\FindWinner;
use App\Core\Domain\Service\PlayersFactory;
use App\Tests\Stubs\History\History;

/**
 * Class Game
 * @package App\Core\Domain\Model\TicTacToe\Game
 */
class Game
{
    const OK = 0;
    const DUPLICATED_PLAYERS_ERROR = 1;
    const DUPLICATED_TURNS_ERROR = 2;
    const GAME_STARTED_BY_PLAYER0_ERROR = 4;
    const PLAYER_IS_NOT_ALLOWED = 8;
    const NON_EXISTING_ERROR = 16;
    const ERRORS = [self::DUPLICATED_PLAYERS_ERROR, self::DUPLICATED_TURNS_ERROR, self::GAME_STARTED_BY_PLAYER0_ERROR, self::PLAYER_IS_NOT_ALLOWED, self::NON_EXISTING_ERROR];

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
     * @param HistoryInterface $history
     * @param PlayersFactory $factory
     * @param FindWinner $findWinner
     * @param EventManagerInterface $eventManager
     * @param string $uuid
     */
    public function __construct(Board $board, HistoryInterface $history, PlayersFactory $factory, FindWinner $findWinner, EventManagerInterface $eventManager, string $uuid)
    {
        $this->board = $board;
        $this->history = $history;
        $this->factory = $factory;
        $this->findWinner = $findWinner;
        $this->players = [];
        $this->errors = self::OK; // Just to remember: such representation of start value explains initial state
        $this->eventManager = $eventManager;
        $this->uuid = $uuid;
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
     * @return HistoryInterface
     */
    public function history(): HistoryInterface
    {
        return $this->history;
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
        return $this->uuid;
    }

    /**
     * @return EventManagerInterface
     */
    public function eventManger(): EventManagerInterface
    {
        return  $this->eventManager;
    }
}
