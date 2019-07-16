<?php
declare(strict_types=1);

namespace App\Tests\integration;

use App\Core\Application\Command\TakeTileService;
use App\Core\Application\Errors\ErrorLog;
use App\Core\Application\Query\FindWinnerService;
use App\Core\Application\Service\PlayerRegistry;
use App\Core\Application\Validation\TurnControl;
use App\Core\Domain\Model\TicTacToe\Game\Board\Board;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\History;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Core\Domain\Service\PlayersFactory;
use App\Entity\Player;
use App\Presentation\Web\Pub\History\History as FrameworkHistory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicGameplayTest extends WebTestCase
{
    /** @var Player[] */
    private $players;
    /** @var TicTacToe */
    private $game;
    /** @var TurnControl */
    private $turnControl;

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

        $this->turnControl = new TurnControl($playerRegistry, $errorLog);
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
}
