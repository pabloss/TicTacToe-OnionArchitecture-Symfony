<?php
declare(strict_types=1);

namespace AppTests\Core\Domain\Model\TicTacToe;

use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use PHPUnit\Framework\TestCase;

class SymbolTest extends TestCase
{
    /**
     * @test
     * @expectedException App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
     */
    public function validate_symbol()
    {
        /**
         * Put here all posible cases of wrong initialization arguments
         */
        $symbol = new Symbol('#');
        $symbol = new Symbol(0);
        $symbol = new Symbol(-1);
        $symbol = new Symbol(null);
        $symbol = new Symbol(new \stdClass());
        $symbol = new Symbol(\json_decode(['x' => 'y']));
    }

    /**
     * @test
     */
    public function get_symbol()
    {
        $symbol = new Symbol('X');
        self::assertEquals('X', $symbol->value());

        $symbol = new Symbol('0');
        self::assertEquals('0', $symbol->value());
    }
}
