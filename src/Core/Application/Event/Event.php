<?php
declare(strict_types=1);

namespace App\Core\Application\Event;

use App\Core\Domain\Event\Params\ParamsInterface;
use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;

/**
 * Class Event
 * @package App\Core\Domain\Event
 */
trait Event
{
    /** @var string */
    private $name;
    /** @var ParamsInterface */
    private $params;

    /**
     * Event constructor.
     * @param string $name
     * @param ParamsInterface|null $params
     */
    public function __construct(string $name, ParamsInterface $params = null)
    {
        $this->name = $name;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return ParamsInterface|null
     */
    public function params(): ?ParamsInterface
    {
        return $this->params;
    }

    /**
     * @return Game|null
     */
    public function game(): ?Game
    {
        if(null === $this->params()){
            return null;
        }
        return $this->params()->game();
    }

    /**
     * @return Player|null
     */
    public function player(): ?Player
    {
        if(null === $this->params()){
            return null;
        }
        return $this->params()->player();
    }

    /**
     * @return Tile|null
     */
    public function tile(): ?Tile
    {
        if(null === $this->params()){
            return null;
        }
        return $this->params()->tile();
    }

    /**
     * @return Board|null
     */
    public function gameBoard(): ?Board
    {
        if(null === $this->params()){
            return null;
        }
        if(null === $this->params->game()){
            return null;
        }
        return $this->params()->game()->board();
    }

    /**
     * @return HistoryInterface|null
     */
    public function gameHistory(): ?HistoryInterface
    {
        if(null === $this->params()){
            return null;
        }
        if(null === $this->params->history()){
            return null;
        }
        return $this->params()->history();
    }

    public function gameErrors(): ?int
    {
        if(null === $this->params()){
            return null;
        }
        if(null === $this->params->game()){
            return null;
        }
        return $this->params()->game()->errors();
    }


}
