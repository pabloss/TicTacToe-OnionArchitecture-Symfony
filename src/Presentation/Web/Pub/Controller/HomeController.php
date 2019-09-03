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
use App\Core\Domain\Service\TurnControl\AccessControl;
use App\Core\Domain\Service\TurnControl\ErrorLog;
use App\Core\Domain\Service\TurnControl\PlayerRegistry;
use App\Core\Domain\Service\TurnControl\TurnControl;
use App\Core\Domain\Service\TurnControl\Validation\GameShouldStartWithCorrectPlayerValidation;
use App\Core\Domain\Service\TurnControl\Validation\PlayerMustNotTakeTakenAlreadyTileValidation;
use App\Core\Domain\Service\TurnControl\Validation\PlayerShouldBeRegisteredValidation;
use App\Core\Domain\Service\TurnControl\Validation\PreviousPlayerShouldBeDifferentThanActualValidation;
use App\Core\Domain\Service\TurnControl\Validation\ValidationCollection;
use App\Presentation\Web\Pub\History\History;
use App\Repository\HistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/game", name="game")
     * @return Response
     */
    public function game(): Response
    {
        return $this->render('@Pub/game/game.html.twig');
    }

    /**
     * @Route("/api/game", name="api.game")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function apiGame(EntityManagerInterface $entityManager): Response
    {
        $histories = $entityManager->getRepository(\App\Entity\History::class)->findBy([], ['createdAt' => 'DESC']);
        $result = [];
        foreach ($histories as $history) {
            $result[$history->getTile()[0]*3+$history->getTile()[1]] = $history->getPlayerSymbol();
        }
        return $this->json($result);
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
        AccessControl::loadRegistry($playerRegistry);
        $turnControl = new TurnControl(new ValidationCollection(
            [
                ErrorLog::PLAYER_IS_NOT_ALLOWED => new PlayerShouldBeRegisteredValidation(),
                ErrorLog::GAME_STARTED_BY_PLAYER0_ERROR => new GameShouldStartWithCorrectPlayerValidation(),
                ErrorLog::DUPLICATED_TURNS_ERROR => new PreviousPlayerShouldBeDifferentThanActualValidation(),
                ErrorLog::DUPLICATED_TILE_ERROR => new PlayerMustNotTakeTakenAlreadyTileValidation(),
            ]
        ), $this->errorLog);
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
            return new JsonResponse([], Response::HTTP_OK);
        } elseif (0 < (int) $this->errorLog->errors($this->game)){
            return new JsonResponse([
                'errors' => (int) $this->errorLog->errors($this->game)
            ], JsonResponse::HTTP_CONFLICT);
        }

    }

    /**
     * @Route("/game/reset", name="reset")
     * @param HistoryRepository $repository
     * @return JsonResponse
     */
    public function reset(HistoryRepository $repository)
    {
        $repository->cleanupRepository();
        return new JsonResponse([]);
    }
}
