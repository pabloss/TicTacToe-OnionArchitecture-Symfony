<?php
declare(strict_types=1);

namespace App\Tests\unit\Core\Domain\Service\TurnControl;

use App\AppCore\DomainModel\Game\Game;
use App\AppCore\DomainServices\TurnControl\ErrorLog;
use PHPUnit\Framework\TestCase;

/**
 * Class ErrorLogTest
 * @package App\Tests\unit\Core\Domain\Service
 */
class ErrorLogTest extends TestCase
{
    /**
     * @test
     */
    public function noErrors()
    {
        $errorLog = new ErrorLog();
        $gameProphecy = $this->prophesize(Game::class);
        $gameProphecy->uuid()->willReturn(0);

        self::assertTrue($errorLog->noErrors($gameProphecy->reveal()));
    }

    /**
     * @test
     */
    public function addError()
    {
        $errorLog = new ErrorLog();
        $gameProphecy = $this->prophesize(Game::class);
        $gameProphecy->uuid()->willReturn(0);
        $errorLog->addError(ErrorLog::DUPLICATED_PLAYERS_ERROR, $gameProphecy->reveal());

        self::assertFalse($errorLog->noErrors($gameProphecy->reveal()));
        self::assertTrue($errorLog->hasError(ErrorLog::DUPLICATED_PLAYERS_ERROR, $gameProphecy->reveal()));
    }

    /**
     * @test
     */
    public function noErrorsTwoGames()
    {
        $errorLog = new ErrorLog();
        $game1Prophecy = $this->prophesize(Game::class);
        $game1Prophecy->uuid()->willReturn(0);
        $game2Prophecy = $this->prophesize(Game::class);
        $game2Prophecy->uuid()->willReturn(1);

        $errorLog->addError(ErrorLog::DUPLICATED_PLAYERS_ERROR, $game2Prophecy->reveal());
        self::assertTrue($errorLog->noErrors($game1Prophecy->reveal()));
        self::assertTrue($errorLog->hasError(ErrorLog::DUPLICATED_PLAYERS_ERROR, $game2Prophecy->reveal()));
        self::assertFalse($errorLog->hasError(ErrorLog::DUPLICATED_PLAYERS_ERROR, $game1Prophecy->reveal()));
    }
}
