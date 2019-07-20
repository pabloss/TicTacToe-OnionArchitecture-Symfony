<?php
declare(strict_types=1);

namespace App\Presentation\Web\Pub\History;

use App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue;
use App\Core\Domain\Model\TicTacToe\Exception\OutOfLegalSizeException;
use App\Core\Domain\Model\TicTacToe\Game\Board\Tile;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\Player\Player;
use App\Core\Domain\Model\TicTacToe\Game\Player\Symbol;
use App\Core\Domain\Service\History\HistoryContent;
use App\Core\Domain\Service\History\HistoryInterface;
use App\Core\Domain\Service\History\HistoryItem;
use App\Repository\HistoryRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use function microtime;

/**
 * Class History
 * @package App\Presentation\Web\Pub\History
 */
class History implements HistoryInterface
{
    const LIMIT = 9;

    /** @var HistoryRepository */
    private $historyRepository;

    /**
     * History constructor.
     * @param HistoryRepository $historyRepository
     */
    public function __construct(HistoryRepository $historyRepository)
    {
        $this->historyRepository = $historyRepository;
    }

    /**
     * @param Game $game
     * @return HistoryContent
     * @throws NotAllowedSymbolValue
     * @throws OutOfLegalSizeException
     */
    public function content(Game $game): HistoryContent
    {
        $histories = $this->historyRepository->findAll();
        $historyItems = [];
        foreach ($histories as $history) {
            $historyItems[] = $this->createHistoryItem($game, $history);
        }

        return new HistoryContent($historyItems);
    }

    /**
     * @param Game $game
     * @param \App\Entity\History|null $entity
     * @return HistoryItem
     * @throws NotAllowedSymbolValue
     * @throws OutOfLegalSizeException
     */
    private function createHistoryItem(Game $game, ?\App\Entity\History $entity): ?HistoryItem
    {
        if (null === $entity) {
            return null;
        }
        $player = new Player(new Symbol($entity->getPlayerSymbol()), $entity->getPlayerUuid());
        $tile = new Tile($entity->getTile()[0], $entity->getTile()[1]);

        $historyItem = new HistoryItem($player, $tile, $game);

        return $historyItem;
    }

    public function lastItemPlayerSymbolValue(Game $game): ?string
    {
        return $this->lastItem($game)->player()->symbol()->value();
    }

    /**
     * @param Game $game
     * @return HistoryItem|null
     * @throws NotAllowedSymbolValue
     * @throws OutOfLegalSizeException
     */
    public function lastItem(Game $game): ?HistoryItem
    {
        if (0 === (int)$this->historyRepository->count(['gameUuid' => $game->uuid()])) {
            return null;
        }
        $entity = $this->historyRepository->getLastByGame($game);

        return $this->createHistoryItem($game, $entity);
    }

    /**
     * @return string
     * @throws NotAllowedSymbolValue
     */
    public function getStartingPlayerSymbolValue(): string
    {
        return $this->getStartingPlayerSymbol()->value();
    }

    /**
     * @return Symbol
     * @throws NotAllowedSymbolValue
     */
    public function getStartingPlayerSymbol(): Symbol
    {
        return new Symbol(Symbol::PLAYER_X_SYMBOL);
    }

    /**
     * @param Game $game
     * @return int
     */
    public function length(Game $game): int
    {
        return $this->historyRepository->count(['gameUuid' => $game->uuid()]);
    }

    /**
     * @param Player $player
     * @param Tile $tile
     * @param Game $game
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveTurn(Player $player, Tile $tile, Game $game): void
    {
        $entity = new \App\Entity\History();
        $entity->setPlayerUuid($player->uuid());
        $entity->setPlayerSymbol($player->symbol()->value());
        $entity->setGameUuid($game->uuid());
        $entity->setCreatedAt((string)microtime(true));
        $entity->setTile([$tile->row(), $tile->column()]);
        $this->historyRepository->save($entity);
    }
}
