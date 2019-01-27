<?php
declare(strict_types=1);

namespace AppTests\Core\Domain\Model\TicTacToe\Domain\Type;

use App\Core\Domain\Model\TicTacToe\Type\PlayerType;
use PHPUnit\Framework\TestCase;

class PlayerTypeTest extends TestCase
{
    /**
     * @test
     * @expectedException App\Core\Domain\Model\TicTacToe\Exception\NotAllowedTypeValue
     */
    public function types()
    {
        $type = new PlayerType('AI');
        self::assertEquals('AI', $type->value());

        $type = new PlayerType('Real');
        self::assertEquals('Real', $type->value());

        $type = new PlayerType('#');
    }
}
