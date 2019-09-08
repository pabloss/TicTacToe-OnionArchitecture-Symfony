<?php


namespace App\Presentation\Web\Pub\Controller;

use App\AppCore\ApplicationServices\FindWinnerService;
use App\AppCore\ApplicationServices\FormatHistoryResult;
use App\AppCore\ApplicationServices\TakeTileService as CoreTakeTileService;
use App\AppCore\DomainModel\Game\Board\Board;
use App\AppCore\DomainModel\Game\Exception\NotAllowedSymbolValue;
use App\AppCore\DomainModel\Game\Exception\OutOfLegalSizeException;
use App\AppCore\DomainModel\Game\Game as TicTacToe;
use App\AppCore\DomainModel\Game\Player\Player;
use App\AppCore\DomainModel\Game\Player\Symbol;
use App\AppCore\DomainModel\History\History;
use App\AppCore\DomainServices\PlayersFactory;
use App\AppCore\DomainServices\TurnControl\AccessControl;
use App\AppCore\DomainServices\TurnControl\ErrorLog;
use App\AppCore\DomainServices\TurnControl\PlayerRegistry;
use App\AppCore\DomainServices\TurnControl\TurnControl;
use App\AppCore\DomainServices\TurnControl\Validation\GameShouldStartWithCorrectPlayerValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PlayerMustNotTakeTakenAlreadyTileValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PlayerShouldBeRegisteredValidation;
use App\AppCore\DomainServices\TurnControl\Validation\PreviousPlayerShouldBeDifferentThanActualValidation;
use App\AppCore\DomainServices\TurnControl\Validation\ValidationCollection;
use App\Presentation\Web\Pub\Service\Command\TakeTileService;
use App\Repository\HistoryRepository;
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

    /** @var Player[]|array  */
    private $players;

    /** @var ErrorLog  */
    private $errorLog;

    /** @var TicTacToe  */
    private $game;

    /** @var array  */
    private $result;

    /** @var FormatHistoryResult */
    private $formatHistoryResultService;

    /**
     * @param PlayersFactory $factory
     * @param PlayerRegistry $playerRegistry
     * @param ErrorLog $errorLog
     * @param History $history
     * @param FormatHistoryResult $formatHistoryResultService
     * @throws NotAllowedSymbolValue
     */
    public function __construct(PlayersFactory $factory, PlayerRegistry $playerRegistry, ErrorLog $errorLog, History $history, FormatHistoryResult $formatHistoryResultService)
    {
        $this->formatHistoryResultService = $formatHistoryResultService;
        $this->result = array_fill(0, 9, null);
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
        $this->takeTileService = new TakeTileService(new CoreTakeTileService($this->game, $history, $turnControl));
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
     * @Route("/game", name="game")
     * @return Response
     */
    public function game(): Response
    {
        return $this->render('@Pub/game/game.html.twig');
    }

    /**
     * @Route("/api/game", name="api.game")
     * @param FormatHistoryResult $boardResult
     * @return Response
     */
    public function apiGame(FormatHistoryResult $boardResult): Response
    {
        return $this->json($boardResult->format($this->game));
    }

    /**
     * @Route("/game/get-tile/{symbol}/{x},{y}", name="take_tile")
     * @param string $symbol
     * @param int $x
     * @param int $y
     * @return Response
     * @throws OutOfLegalSizeException
     * @throws NotAllowedSymbolValue
     */
    public function getTile(string $symbol, int $x, int $y)
    {
        $this->takeTileService->takeTile(
            $this->players[$symbol]->symbolValue(),
            $this->players[$symbol]->uuid(),
            $x, $y
        );
        if(0 === (int) $this->errorLog->errors($this->game)){
            return new JsonResponse($this->formatHistoryResultService->format($this->game), Response::HTTP_OK);
        } elseif (0 < (int) $this->errorLog->errors($this->game)){
            return new JsonResponse([
                'errors' => (int) $this->errorLog->errors($this->game)
            ], JsonResponse::HTTP_CONFLICT);
        }

    }

    /**
     * @Route("/game/get-winner", name="get.winner")
     * @param FindWinnerService $service
     * @return JsonResponse
     * @throws NotAllowedSymbolValue
     */
    public function getWinner(FindWinnerService $service)
    {
        $winner = $service->winner(
            new TicTacToe(
                Board::fromContents($this->formatHistoryResultService->format($this->game)),
                $this->game->uuid()
            )
        );
        return new JsonResponse($winner ? $winner->symbolValue(): null);
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
