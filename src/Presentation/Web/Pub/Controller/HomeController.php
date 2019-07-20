<?php


namespace App\Presentation\Web\Pub\Controller;

use App\Core\Application\Command\TakeTileService;
use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Exception\OutOfLegalSizeException;
use App\Core\Domain\Model\TicTacToe\Game\Board\Board;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Core\Domain\Service\PlayersFactory;
use App\Core\Domain\Service\TurnControl\ErrorLog;
use App\Core\Domain\Service\TurnControl\PlayerRegistry;
use App\Core\Domain\Service\TurnControl\TurnControl;
use App\Presentation\Web\Pub\History\History;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Presentation\Web\Pub\Controller
 */
final class HomeController extends AbstractController
{
    /** @var TakeTileService */
    private $takeTileService;
    private $players;
    private $errorLog;
    private $game;

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
     * @param PlayersFactory $factory
     * @param PlayerRegistry $playerRegistry
     * @param ErrorLog $errorLog
     * @param History $history
     * @throws NotAllowedSymbolValue
     */
    public function __construct(PlayersFactory $factory, PlayerRegistry $playerRegistry, ErrorLog $errorLog, History $history)
    {
        $this->errorLog = $errorLog;
        $this->game = new TicTacToe(new Board(), '1');
        $this->players = $factory->create();
        $playerRegistry->registerPlayer(
            $this->players[Symbol::PLAYER_X_SYMBOL],
            $this->game
        );
        $playerRegistry->registerPlayer(
            $this->players[Symbol::PLAYER_0_SYMBOL],
            $this->game
        );
        $turnControl = new TurnControl($playerRegistry, $this->errorLog);
        $this->takeTileService = new TakeTileService($this->game, $history, $turnControl);
    }

    /**
     * @Route("/game/get-tile/{symbol}/{x},{y}", name="take_tile")
     * @param string $symbol
     * @param int $x
     * @param int $y
     * @return Response
     * @throws NotAllowedSymbolValue
     * @throws OutOfLegalSizeException
     */
    public function getTile(string $symbol, int $x, int $y)
    {
        $this->takeTileService->takeTile($this->players[$symbol], new Tile($x, $y));
        if(0 === (int) $this->errorLog->errors($this->game)){
            return new Response('', Response::HTTP_OK);
        } elseif (2 === (int) $this->errorLog->errors($this->game)){
            return new Response('', Response::HTTP_CONFLICT);
        }

    }
}
