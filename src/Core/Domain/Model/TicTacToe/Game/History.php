<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\Game;

use App\Core\Domain\Model\TicTacToe\ValueObject\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;

/**
 * Class History
 * @package App\Core\Domain\Model\TicTacToe\Game
 */
class History
{
    const LIMIT = 9;

    /**
     * @var array
     */
    private $timeLine = [];

    /**
     * @var Symbol
     */
    private $startingPlayerSymbol;

    /**
     * @var
     */
    private $lastTurn;

    /**
     * History constructor.
     */
    public function __construct()
    {
        $this->startingPlayerSymbol = new Symbol(Symbol::PLAYER_X_SYMBOL);
    }

    /**
     * @return array
     */
    public function getLast(): array
    {
        return \end($this->timeLine);
    }

    /**
     * @return array
     */
    public function &content(): array
    {
        return $this->timeLine;
    }

    /**
     * @return mixed
     */
    public function getLastTurn(): ?Symbol
    {
        return $this->lastTurn;
    }

    /**
     * @return Symbol
     */
    public function getStartingPlayerSymbol(): Symbol
    {
        return $this->startingPlayerSymbol;
    }

    /**
     * @param Player $player
     * @param Game $game
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
     */
    public function saveLastTurn(Player $player, Game $game): void
    {
        if (
            !empty($this->lastTurn) &&
            $player->symbol() === $this->lastTurn
        ) {
            $game->addError(Game::DUPLICATED_TURNS_ERROR, $player);
        }
        $this->lastTurn = $player->symbol();
    }

    /**
     * @param Tile $tile
     */
    public function saveTurnToHistory(Tile $tile): void
    {
        $this->set([$tile->row(), $tile->column()]);
    }

    /**
     * @param $value
     */
    public function set($value): void
    {
        $this->timeLine[$this->length() % self::LIMIT] = $value;
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return \count($this->timeLine);
    }
}
