<?php
declare(strict_types=1);

namespace App\Tests\integration\Core\Domain\Model\TicTacToe\Game;

use App\AppCore\ApplicationServices\FindWinnerService;
use App\AppCore\ApplicationServices\TakeTileService;
use App\AppCore\DomainModel\Game\Board\Board;
use App\AppCore\DomainModel\Game\Board\Tile;
use App\AppCore\DomainModel\Game\Game as TicTacToe;
use App\AppCore\DomainModel\Game\Player\Symbol;
use App\AppCore\DomainModel\History\History as FrameworkHistory;
use App\AppCore\DomainServices\PlayersFactory;
use App\AppCore\DomainServices\TurnControl\ErrorLog;
use App\AppCore\DomainServices\TurnControl\PlayerRegistry;
use App\AppCore\DomainServices\TurnControl\TurnControl;
use App\AppCore\DomainServices\TurnControl\Validation\GameShouldStartWithCorrectPlayerValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PlayerMustNotTakeTakenAlreadyTileValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PlayerShouldBeRegisteredValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PreviousPlayerShouldBeDifferentThanActualValidation;
use App\AppCore\DomainServices\TurnControl\Validation\ValidationCollection;
use App\Entity\Player;
use App\Tests\Stubs\History\History;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicGameplayTest extends WebTestCase
{
    /** @var Player[] */
    private $players;
    /** @var TicTacToe */
    private $game;
    /** @var TurnControl */
    private $turnControl;

    /**
     * @test
     *
     * End result:
     * 0X0
     * -X-
     * -X-
     *
     * Hint: best way of solving a problem is making sure that the problem does
     * not exist in the first place
     */
    public function complete_happy_path_gameplay()
    {
        $history = new History();
        $takeTileService = new TakeTileService($this->game, $history, $this->turnControl);
        $playerX = $this->players[Symbol::PLAYER_X_SYMBOL];
        $player0 = $this->players[Symbol::PLAYER_0_SYMBOL];

        $takeTileService->takeTile($playerX, new Tile(1, 1));
        $takeTileService->takeTile($player0, new Tile(0, 0));
        $takeTileService->takeTile($playerX, new Tile(0, 1));
        $takeTileService->takeTile($player0, new Tile(0, 2));
        $takeTileService->takeTile($playerX, new Tile(2, 1));

        $findWinner = new FindWinnerService();
        $this->assertSame($playerX, $findWinner->winner($this->game));
    }

    /**
     * @test
     */
    public function complete_happy_path_gameplay_other_player_wins()
    {
        // We are swapping players
        $history = new History();
        $takeTileService = new TakeTileService($this->game, $history, $this->turnControl);
        $playerX = $this->players[Symbol::PLAYER_X_SYMBOL];
        $player0 = $this->players[Symbol::PLAYER_0_SYMBOL];

        $takeTileService->takeTile($playerX, new Tile(2, 2));
        $takeTileService->takeTile($player0, new Tile(1, 1));
        $takeTileService->takeTile($playerX, new Tile(0, 0));
        $takeTileService->takeTile($player0, new Tile(0, 1));
        $takeTileService->takeTile($playerX, new Tile(0, 2));
        $takeTileService->takeTile($player0, new Tile(2, 1));

        $findWinner = new FindWinnerService();
        $this->assertSame($player0, $findWinner->winner($this->game));
    }

    /**
     * @test
     *
     * End result:
     * 0X0
     * -X-
     * -X-
     *
     * Hint: best way of solving a problem is making sure that the problem does
     * not exist in the first place
     */
    public function complete_happy_path_gameplay_use_another_implementation()
    {
        $client = self::createClient();
        $history = $client->getContainer()->get(FrameworkHistory::class);
        $takeTileService = new TakeTileService($this->game, $history, $this->turnControl);
        $playerX = $this->players[Symbol::PLAYER_X_SYMBOL];
        $player0 = $this->players[Symbol::PLAYER_0_SYMBOL];

        $takeTileService->takeTile($playerX, new Tile(1, 1));
        $takeTileService->takeTile($player0, new Tile(0, 0));
        $takeTileService->takeTile($playerX, new Tile(0, 1));
        $takeTileService->takeTile($player0, new Tile(0, 2));
        $takeTileService->takeTile($playerX, new Tile(2, 1));
        $findWinner = new FindWinnerService();
        $this->assertSame($playerX, $findWinner->winner($this->game));
    }

    /**
     * @test
     */
    public function complete_happy_path_gameplay_other_player_wins_use_another_implementation()
    {
        $client = self::createClient();

        // We are swapping players
        $history = $client->getContainer()->get(FrameworkHistory::class);
        $takeTileService = new TakeTileService($this->game, $history, $this->turnControl);
        $playerX = $this->players[Symbol::PLAYER_X_SYMBOL];
        $player0 = $this->players[Symbol::PLAYER_0_SYMBOL];

        $takeTileService->takeTile($playerX, new Tile(2, 2));
        $takeTileService->takeTile($player0, new Tile(1, 1));
        $takeTileService->takeTile($playerX, new Tile(0, 0));
        $takeTileService->takeTile($player0, new Tile(0, 1));
        $takeTileService->takeTile($playerX, new Tile(0, 2));
        $takeTileService->takeTile($player0, new Tile(2, 1));
        $findWinner = new FindWinnerService();
        $this->assertSame($player0, $findWinner->winner($this->game));
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
