<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\Game;

use App\Core\Application\Service\AccessControl;
use App\Core\Domain\Model\TicTacToe\Exception;
use App\Core\Domain\Model\TicTacToe\ValueObject\Player;
use App\Core\Domain\Service\FindWinner;
use App\Core\Domain\Service\PlayersFactory;

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

    /**
     * Game constructor.
     * @param Board $board
     * @param History $history
     * @param PlayersFactory $factory
     * @param FindWinner $findWinner
     */
    public function __construct(Board $board, History $history, PlayersFactory $factory, FindWinner $findWinner)
    {
        $this->board = $board;
        $this->history = $history;
        $this->factory = $factory;
        $this->findWinner = $findWinner;

        $this->players = [];
        $this->errors = self::OK; // Just to remember: such representation of start value explains initial state
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
     * @return History
     */
    public function &history(): History
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
}
