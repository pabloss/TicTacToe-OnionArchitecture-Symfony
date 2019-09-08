<?php
declare(strict_types=1);

namespace App\Tests\integration\Core\Domain\Model\TicTacToe\Game\Player;

use App\AppCore\ApplicationServices\FindWinnerService;
use App\AppCore\DomainModel\Game\AI\AI;
use App\AppCore\DomainModel\Game\Board\Board;
use App\AppCore\DomainModel\Game\Game as TicTacToe;
use App\AppCore\DomainModel\Game\Player\Player;
use App\AppCore\DomainModel\Game\Player\Symbol;
use App\AppCore\DomainServices\PlayersFactory;
use App\AppCore\DomainServices\TakeTileService;
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

class PlayingAgainstAISimulationTest extends TestCase
{
    /** @var \App\Entity\Player[] */
    private $players;
    /** @var TicTacToe */
    private $game;
    /** @var TurnControl */
    private $turnControl;

    /**
     * @test
     */
    public function random_looped_taken_tiles_should_fill_whole_board()
    {
        $history = new History();
        $playersFactory = new PlayersFactory();
        $findWinnerService = new FindWinnerService();
        $takeTileService = new TakeTileService($this->game, $history, $this->turnControl);
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $playersFactory->create();;
        $ai = new AI($this->game);
        for ($i = 2; $i <= 9; $i += 2) {
            $takeTileService->takeTile($playerX, $ai->takeRandomFreeTile());
            /** @var Player $player0 */
            $takeTileService->takeTile($player0, $this->simulate_choosing_tiles_of_real_player());
        }

        self::assertTrue(
            is_null($findWinnerService->winner($this->game)) ||
            $findWinnerService->winner($this->game)->symbol()->value() === 'X' ||
            $findWinnerService->winner($this->game)->symbol()->value() === '0'
        );
        self::assertTrue(
            array_reduce(
                $this->game->board()->contents(),
                function ($carry, $value) {
                    $carry = $carry || (is_null($value) === false);

                    return $carry;
                },
                false
            )
        );
    }

    private function simulate_choosing_tiles_of_real_player()
    {
        $ai = new AI($this->game);

        return $ai->takeRandomFreeTile();
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

        $this->turnControl = new TurnControl(new ValidationCollection(
            [
                ErrorLog::PLAYER_IS_NOT_ALLOWED => new PlayerShouldBeRegisteredValidation(),
                ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR => new GameShouldStartWithCorrectPlayerValidation(),
                ErrorLog::DUPLICATED_TURNS_ERROR => new PreviousPlayerShouldBeDifferentThanActualValidation(),
                ErrorLog::DUPLICATED_TILE_ERROR => new PlayerMustNotTakeTakenAlreadyTileValidation(),
            ]
        ), $errorLog);
    }
}
