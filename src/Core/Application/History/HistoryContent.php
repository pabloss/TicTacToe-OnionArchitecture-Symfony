<?php
declare(strict_types=1);

namespace App\Core\Application\History;

use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Tests\Stubs\History\HistoryItem;

/**
 * Class HistoryContent
 * @package App\Core\Application\History
 */
class HistoryContent extends \ArrayObject
{
    /** @var HistoryItem[] $content */
    private $content;

    /**
     * HistoryContent constructor.
     * @param array $input
     * @param int $flags
     * @param string $iterator_class
     */
    public function __construct($input = array(), $flags = 0, $iterator_class = "ArrayIterator")
    {
        parent::__construct($input, $flags, $iterator_class);
        $this->content = $input;
    }

    /**
     * @return array
     */
    public function getTilesHistory(): array
    {
        $historyTiles = [];
        $historyItems = $this->content;
        /** @var HistoryItem $historyItem */
        foreach ($historyItems as $historyItem) {
            $tile = $historyItem->tile();
            $historyTiles[] = [$tile->row(), $tile->column()];
        }
        return $historyTiles;
    }
}
