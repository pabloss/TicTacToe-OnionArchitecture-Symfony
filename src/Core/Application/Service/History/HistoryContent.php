<?php
declare(strict_types=1);

namespace App\Core\Application\Service\History;

/**
 * Class HistoryContent
 * @package App\Core\Application\Service\History
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
     * @return array[]
     */
    public function getTilesHistory(): array
    {
        $historyTiles = [];
        foreach ($this->content() as $historyItem) {
            $historyTiles[] = $historyItem->getTileArray();
        }
        return $historyTiles;
    }

    /**
     * @return HistoryItem[]
     */
    private function content(): array
    {
        return $this->content;
    }
}
