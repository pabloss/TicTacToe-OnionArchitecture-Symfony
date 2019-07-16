<?php
declare(strict_types=1);

namespace App\Tests\integration;

use App\Core\Application\Command\TakeTileService;
use App\Core\Application\Service\History\History;
use App\Core\Application\Service\PlayersFactory;
use App\Core\Application\Service\TurnControl\ErrorLog;
use App\Core\Application\Service\TurnControl\PlayerRegistry;
use App\Core\Application\Service\TurnControl\TurnControl;
use App\Core\Domain\Model\TicTacToe\AI\AI;
use App\Core\Domain\Model\TicTacToe\Game\Board\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    /** @var \App\Entity\Player[] */
    private $players;
    /** @var TicTacToe */
    private $game;
    /** @var \App\Core\Application\Service\TurnControl\TurnControl */
    private $turnControl;

    /** @var \App\Core\Application\Service\TurnControl\ErrorLog */
    private $errorLog;

    protected function setUp()
    {
        $playerRegistry = new PlayerRegistry();
        $playersFactory = new PlayersFactory();
        $this->players = $playersFactory->create();
        $this->game = new TicTacToe(new Board());
        $playerRegistry->registerPlayer(
            $this->players[Symbol::PLAYER_X_SYMBOL],
            $this->game
        );
        $playerRegistry->registerPlayer(
            $this->players[Symbol::PLAYER_0_SYMBOL],
            $this->game
        );
        $errorLog = new ErrorLog();
        $this->errorLog = $errorLog;

        $this->turnControl = new TurnControl($playerRegistry, $this->errorLog);
    }

    /**
     * @test
     */
    public function looping_AI_player_fills_whole_board_in_9_turns()
    {
        $game  = $this->game;
        $ai = new AI($game);
        $history = new History();
        $takeTileService = new TakeTileService($this->game, $history, $this->turnControl);

        list(Symbol::PLAYER_X_SYMBOL => $player, Symbol::PLAYER_0_SYMBOL => $notUsedPlayer) = $this->players;
        for (
            $expectedFilledCount = 2;
            $expectedFilledCount <= 9;
            $expectedFilledCount += 2
        ) {
            $takeTileService->takeTile($player, $ai->takeRandomFreeTile());
            $takeTileService->takeTile($notUsedPlayer, $this->simulate_choosing_tiles_of_real_player());
            $actualFilledCount = \array_reduce(
                $this->game->board()->contents(),
                function ($carry, $item) {
                    if (null !== $item) {
                        $carry++;
                    }

                    return $carry;
                },
                0
            );

            $allFreeTileIndexes = [];
            $allFilledTileIndexes = [];
            \array_walk(
                $this->game->board()->contents(),
                function ($value, $key) use (&$allFreeTileIndexes, &$allFilledTileIndexes) {
                    if (null === $value) {
                        $allFreeTileIndexes[] = $key;
                    } else {
                        $allFilledTileIndexes[] = $key;
                    }
                }
            );
            self::assertEquals(0, \count(\array_intersect($allFreeTileIndexes, $allFilledTileIndexes)));
            self::assertEquals(9, \count($allFreeTileIndexes) + \count($allFilledTileIndexes));
            self::assertEquals(
                $expectedFilledCount,
                $actualFilledCount
            );
        }
    }

    private function simulate_choosing_tiles_of_real_player()
    {
        $ai = new AI($this->game);

        return $ai->takeRandomFreeTile();
    }
}
