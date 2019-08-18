<?php
declare(strict_types=1);

namespace App\Tests\Stubs\History;

use App\Core\Domain\Service\History\HistoryInterface;

/**
 * Class History
 * @package App\Core\Domain\Model\TicTacToe\Game
 */
class History extends \App\Core\Domain\Service\History\History implements HistoryInterface
{

    const LIMIT = 9;

    public function getTurn($game, $index)
    {
        return $this->timeLine[$game->uuid()][($this->length($game) - 1 - $index) % self::LIMIT];
    }

}
