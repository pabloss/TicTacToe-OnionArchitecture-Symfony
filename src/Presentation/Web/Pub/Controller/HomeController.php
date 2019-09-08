<?php


namespace App\Presentation\Web\Pub\Controller;

use App\Core\Application\Command\TakeTileService;
use App\Core\Application\Query\FindWinnerService;
use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Exception\OutOfLegalSizeException;
use App\Core\Domain\Model\TicTacToe\Game\Board\Board;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\AppCore\DomainModel\Game\Game as TicTacToe;
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
use App\Presentation\Web\Pub\Service\FormatHistoryResult;
use App\Presentation\Web\Pub\Service\History\History;
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
    private $players;
    private $errorLog;
    /** @var TicTacToe  */
    private $game;

    /** @var array  */
    private $result;

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
        return $this->json($boardResult->format());
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
        $this->takeTileService = new TakeTileService($this->game, $history, $turnControl);
    }

    /**
     * @Route("/game/get-tile/{symbol}/{x},{y}", name="take_tile")
     * @param string $symbol
     * @param int $x
     * @param int $y
     * @param FormatHistoryResult $formatHistoryResultService
     * @return Response
     * @throws NotAllowedSymbolValue
     * @throws OutOfLegalSizeException
     */
    public function getTile(string $symbol, int $x, int $y, FormatHistoryResult $formatHistoryResultService)
    {
        $this->takeTileService->takeTile($this->players[$symbol], new Tile($x, $y));
        if(0 === (int) $this->errorLog->errors($this->game)){
            return new JsonResponse($formatHistoryResultService->format(), Response::HTTP_OK);
        } elseif (0 < (int) $this->errorLog->errors($this->game)){
            return new JsonResponse([
                'errors' => (int) $this->errorLog->errors($this->game)
            ], JsonResponse::HTTP_CONFLICT);
        }

    }

    /**
     * @Route("/game/get-winner", name="get.winner")
     * @param FindWinnerService $service
     * @param FormatHistoryResult $result
     * @return JsonResponse
     * @throws NotAllowedSymbolValue
     */
    public function getWinner(FindWinnerService $service, FormatHistoryResult $result)
    {
        $winner = $service->winner(new TicTacToe(
             Board::fromContents($result->format()),
            '1'
        ));
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
