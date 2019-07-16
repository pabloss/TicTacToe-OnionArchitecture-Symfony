<?php


namespace App\Presentation\Web\Pub\Controller;

use App\Core\Application\Command\TakeTileService;
use App\Core\Application\Errors\ErrorLog;
use App\Core\Application\Service\PlayerRegistry;
use App\Core\Application\Validation\TurnControl;
use App\Core\Domain\Model\TicTacToe\Game\Board\Board;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Core\Domain\Service\PlayersFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomeController
 * @package App\Presentation\Web\Pub\Controller
 */
final class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('@Pub/home/index.html.twig');
    }

    /**
     * @Route("/game", name="home")
     * @return Response
     */
    public function game(): Response
    {
        return $this->render('@Pub/home/game.html.twig');
    }

    /**
     * @Route("/game/get-tile/{x},{y}", name="take_tile")
     * @param int $x
     * @param int $y
     * @param PlayersFactory $factory
     * @return Response
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\OutOfLegalSizeException
     */
    public function getTile(int $x, int $y, PlayersFactory $factory, PlayerRegistry $playerRegistry, ErrorLog $errorLog, \App\Presentation\Web\Pub\History\History $history)
    {
        $game = new TicTacToe(new Board());
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $factory->create();
        $playerRegistry->registerPlayer(
            $playerX,
            $game
        );
        $playerRegistry->registerPlayer(
            $player0,
            $game
        );
        $turnControl = new TurnControl($playerRegistry, $errorLog);
        $takeTileService = new TakeTileService($game, $history, $turnControl);
        $takeTileService->takeTile($playerX, new Tile($x, $y));
        return new Response();
    }
}
