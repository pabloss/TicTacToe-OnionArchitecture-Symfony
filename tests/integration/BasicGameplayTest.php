<?php
declare(strict_types=1);

namespace App\Tests\integration;

use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\History;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use App\Core\Domain\Service\FindWinner;
use App\Core\Domain\Service\PlayersFactory;
use App\Tests\Stubs\Event\EventManager;
use App\Tests\Stubs\EventSubscriber\TakeTileEventSubscriber;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicGameplayTest extends WebTestCase
{

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
        $game = new TicTacToe(new Board(), new History(), new PlayersFactory(EventManager::getInstance([new TakeTileEventSubscriber()])), new FindWinner());
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $game->players();
        $playerX->takeTile(new Tile(1, 1), $game);
        $player0->takeTile(new Tile(0, 0), $game);
        $playerX->takeTile(new Tile(0, 1), $game);
        $player0->takeTile(new Tile(0, 2), $game);
        $playerX->takeTile(new Tile(2, 1), $game);
        $this->assertSame($playerX, $game->winner());
    }

    /**
     * @test
     */
    public function complete_happy_path_gameplay_other_player_wins()
    {
        // We are swapping players
        $game = new TicTacToe(new Board(), new History(), new PlayersFactory(EventManager::getInstance([new TakeTileEventSubscriber()])), new FindWinner());
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $game->players();
        $playerX->takeTile(new Tile(2, 2), $game);
        $player0->takeTile(new Tile(1, 1), $game);
        $playerX->takeTile(new Tile(0, 0), $game);
        $player0->takeTile(new Tile(0, 1), $game);
        $playerX->takeTile(new Tile(0, 2), $game);
        $player0->takeTile(new Tile(2, 1), $game);
        $this->assertSame($player0, $game->winner());
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

        $game = new TicTacToe(new Board(), new History(), new PlayersFactory($client->getContainer()->get('App\Tests\Stubs\Event\Framework\EventManager')), new FindWinner());
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $game->players();
        $playerX->takeTile(new Tile(1, 1), $game);
        $player0->takeTile(new Tile(0, 0), $game);
        $playerX->takeTile(new Tile(0, 1), $game);
        $player0->takeTile(new Tile(0, 2), $game);
        $playerX->takeTile(new Tile(2, 1), $game);
        $this->assertSame($playerX, $game->winner());
    }

    /**
     * @test
     */
    public function complete_happy_path_gameplay_other_player_wins_use_another_implementation()
    {
        $client = self::createClient();
        
        // We are swapping players
        $game = new TicTacToe(new Board(), new History(), new PlayersFactory($client->getContainer()->get('App\Tests\Stubs\Event\Framework\EventManager')), new FindWinner());
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $game->players();
        $playerX->takeTile(new Tile(2, 2), $game);
        $player0->takeTile(new Tile(1, 1), $game);
        $playerX->takeTile(new Tile(0, 0), $game);
        $player0->takeTile(new Tile(0, 1), $game);
        $playerX->takeTile(new Tile(0, 2), $game);
        $player0->takeTile(new Tile(2, 1), $game);
        $this->assertSame($player0, $game->winner());
    }
}
