<?php
declare(strict_types=1);

namespace App\AppCore\DomainModel\Game\Board;

use App\AppCore\DomainModel\Game\Player\Player;
use App\AppCore\DomainModel\Game\Player\PlayerInterface;
use App\AppCore\DomainModel\Game\Player\Symbol;

/**
 * Class Board
 * @package App\Core\Domain\Model\TicTacToe\Game
 */
class Board
{
    /** @var array */
    private $board;

    /**
     * Board constructor.
     */
    public function __construct()
    {
        $this->board = array_fill(0, 9, null);
    }
    
    public static function fromContents(array $contents)
    {
        $board = new self();
        /**
         * @var  $index
         * @var Player $content
         */
        foreach ($contents as $index => $content){
            $board->mark(
                new Tile(($index - ($index % 3)) / 3, $index % 3),
                new Player(new Symbol($content), '1')
            );
        }
        //  [($index - ($index % 3)) / 3 , $index % 3]
        return $board;
    }

    /**
     * @param TileInterface $tile
     * @param PlayerInterface $player
     */
    public function mark(TileInterface $tile, PlayerInterface $player): void
    {
        $this->board[$tile->column() + 3 * $tile->row()] = $player;
    }

    /**
     * @return array
     */
    public function &contents(): array
    {
        return $this->board;
    }

    /**
     * @param TileInterface $tile
     * @return mixed
     */
    public function getPlayer(TileInterface $tile)
    {
        return $this->board[$tile->column() + 3 * $tile->row()];
    }
}
