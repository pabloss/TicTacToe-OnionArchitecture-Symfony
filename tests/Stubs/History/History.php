<?php
declare(strict_types=1);

namespace App\Tests\Stubs\History;

use App\Core\Domain\Model\TicTacToe\Game\History as CoreHistory;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;

/**
 * Class History
 * @package App\Core\Domain\Model\TicTacToe\Game
 */
class History extends CoreHistory implements HistoryInterface
{

    const LIMIT = 9;

    public function getTurn($game, $index)
    {
        return $this->timeLine[$game->uuid()][($this->length($game) - 1 - $index) % self::LIMIT];
    }

}
