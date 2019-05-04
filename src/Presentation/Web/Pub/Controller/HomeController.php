<?php


namespace App\Presentation\Web\Pub\Controller;

use App\Core\Domain\Model\TicTacToe\Game\Board;
use App\Core\Domain\Model\TicTacToe\Game\Game as TicTacToe;
use App\Core\Domain\Model\TicTacToe\Game\History;
use App\Core\Domain\Service\FindWinner;
use App\Core\Domain\Service\PlayersFactory;
use App\Tests\Stubs\Event\Framework\EventManager;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Presentation\Web\Pub\Controller
 * @not used currently
 */
final class HomeController extends AbstractController
{
    /** @var EventManager */
    private $eventManager;

    /**
     * HomeController constructor.
     * @param EventManager $eventManager
     */
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }


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
     * @return Response
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\OutOfLegalSizeException
     */
    public function getTile(int $x, int $y)
    {
        $game = new TicTacToe(new Board(), new History(), new PlayersFactory(), new FindWinner(),
        $this->eventManager,
        \uniqid()
        );
        list(Symbol::PLAYER_X_SYMBOL => $playerX, Symbol::PLAYER_0_SYMBOL => $player0) = $game->players();
        $playerX->takeTile(new Tile($x, $y), $game);
        return new Response();
    }
}
