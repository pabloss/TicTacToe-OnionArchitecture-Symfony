<?php
declare(strict_types=1);

namespace App\Tests\integration\Core\Domain\Model\TicTacToe\Game\Player;

use App\AppCore\ApplicationServices\TakeTileService;
use App\AppCore\DomainModel\Game\AI\AI;
use App\AppCore\DomainModel\Game\Board\Board;
use App\AppCore\DomainModel\Game\Game as TicTacToe;
use App\AppCore\DomainModel\Game\Player\Player;
use App\AppCore\DomainModel\Game\Player\Symbol;
use App\AppCore\DomainServices\PlayersFactory;
use App\AppCore\DomainServices\TurnControl\ErrorLog;
use App\AppCore\DomainServices\TurnControl\PlayerRegistry;
use App\AppCore\DomainServices\TurnControl\TurnControl;
use App\AppCore\DomainServices\TurnControl\Validation\GameShouldStartWithCorrectPlayerValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PlayerMustNotTakeTakenAlreadyTileValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PlayerShouldBeRegisteredValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PreviousPlayerShouldBeDifferentThanActualValidation;
use App\AppCore\DomainServices\TurnControl\Validation\ValidationCollection;
use App\Tests\Stubs\History\History;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    /** @var \App\Entity\Player[] */
    private $players;
    /** @var TicTacToe */
    private $game;
    /** @var TurnControl */
    private $turnControl;

    /** @var ErrorLog */
    private $errorLog;

    /**
     * @test
     */
    public function looping_AI_player_fills_whole_board_in_9_turns()
    {
        $game = $this->game;
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
            $actualFilledCount = array_reduce(
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
            array_walk(
                $this->game->board()->contents(),
                function ($value, $key) use (&$allFreeTileIndexes, &$allFilledTileIndexes) {
                    if (null === $value) {
                        $allFreeTileIndexes[] = $key;
                    } else {
                        $allFilledTileIndexes[] = $key;
                    }
                }
            );
            self::assertEquals(0, count(array_intersect($allFreeTileIndexes, $allFilledTileIndexes)));
            self::assertEquals(9, count($allFreeTileIndexes) + count($allFilledTileIndexes));
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

    /**
     * @test
     */
    public function player_has_symbol()
    {
        $symbol = new Symbol(Symbol::PLAYER_X_SYMBOL);

        $player = new Player($symbol, uniqid());
        self::assertEquals($symbol, $player->symbol());
    }

    protected function setUp()
    {
        $playerRegistry = new PlayerRegistry();
        $playersFactory = new PlayersFactory();
        $this->players = $playersFactory->create();
        $this->game = new TicTacToe(new Board(), \uniqid());
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

        $this->turnControl = new TurnControl(new ValidationCollection(
            [
                ErrorLog::PLAYER_IS_NOT_ALLOWED => new PlayerShouldBeRegisteredValidation(),
                ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR => new GameShouldStartWithCorrectPlayerValidation(),
                ErrorLog::DUPLICATED_TURNS_ERROR => new PreviousPlayerShouldBeDifferentThanActualValidation(),
                ErrorLog::DUPLICATED_TILE_ERROR => new PlayerMustNotTakeTakenAlreadyTileValidation(),
            ]
        ), $this->errorLog);
    }
}
