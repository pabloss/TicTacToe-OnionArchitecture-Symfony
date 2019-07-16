<?php
declare(strict_types=1);

namespace App\Core\Domain\Model\TicTacToe\Game\Player;

use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;

/**
 * Class Symbol
 * @package App\Core\Domain\Model\TicTacToe\ValueObject
 */
class Symbol
{
    const PLAYER_X_SYMBOL = 'X';
    const PLAYER_0_SYMBOL = '0';

    /**
     * @var
     */
    private $value;

    /**
     * Symbol constructor.
     * @param $value
     * @throws NotAllowedSymbolValue
     */
    public function __construct($value)
    {
        if (!in_array(
            $value,
            [
                self::PLAYER_X_SYMBOL,
                self::PLAYER_0_SYMBOL,
            ],
            true
        )) {
            throw new NotAllowedSymbolValue();
        }

        $this->value = $value;
    }


    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }
}
