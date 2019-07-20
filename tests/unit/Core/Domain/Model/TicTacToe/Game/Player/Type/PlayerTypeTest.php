<?php
declare(strict_types=1);

namespace App\Tests\unit\Core\Domain\Model\TicTacToe\Game\Player\Type;

use App\Core\Domain\Model\TicTacToe\Game\Player\Type\PlayerType;
use App\Tests\integration\Core\Domain\Model\TicTacToe\Domain\Game\Player\Type\App;
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
