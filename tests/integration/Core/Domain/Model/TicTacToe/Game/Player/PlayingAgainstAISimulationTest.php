<?php
declare(strict_types=1);

namespace App\Tests\integration\Core\Domain\Model\TicTacToe\Game\Player;

use App\Core\Application\Command\TakeTileService;
use App\Core\Application\Query\FindWinnerService;
use App\Core\Domain\Model\TicTacToe\AI\AI;
use App\Core\Domain\Model\TicTacToe\Game\Board\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Core\Domain\Service\History\History;
use App\Core\Domain\Service\PlayersFactory;
use App\Core\Domain\Service\TurnControl\ErrorLog;
use App\Core\Domain\Service\TurnControl\PlayerRegistry;
use App\Core\Domain\Service\TurnControl\TurnControl;
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
}
