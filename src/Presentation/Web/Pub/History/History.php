<?php
declare(strict_types=1);

namespace App\Presentation\Web\Pub\History;

use App\Core\Application\History\HistoryContent;
use App\Core\Domain\Model\TicTacToe\Game\Game;
use App\Core\Domain\Model\TicTacToe\Game\HistoryInterface;
use App\Core\Domain\Model\TicTacToe\Game\Player;
use App\Core\Domain\Model\TicTacToe\ValueObject\Symbol;
use App\Core\Domain\Model\TicTacToe\ValueObject\Tile;
use App\Repository\HistoryRepository;
use App\Tests\Stubs\History\HistoryItem;

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
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\OutOfLegalSizeException
     */
    public function content(Game $game): HistoryContent
    {
        $histories = $this->historyRepository->findAll();
        $historyItems = [];
        foreach ($histories as $history){
            $historyItems[] = $this->createHistoryItem($game, $history);
        }

        return new HistoryContent($historyItems);
    }

    /**
     * @param Game $game
     * @return HistoryItem|null
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\OutOfLegalSizeException
     */
    public function getLastTurn(Game $game): ?HistoryItem
    {
        if(0 === (int) $this->historyRepository->count([])){
            return null;
        }
        $entity = $this->historyRepository->getLastByGame($game);

        return  $this->createHistoryItem($game, $entity);
    }

    /**
     * @param Game $game
     * @param int $index
     * @return HistoryItem|null
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\OutOfLegalSizeException
     */
    public function getTurn(Game $game, int $index): ?HistoryItem
    {
        if(0 === (int) $this->historyRepository->count([])){
            return null;
        }
        $entity = $this->historyRepository->getLastByGameAndIndex($game, $index);

        return  $this->createHistoryItem($game, $entity);
    }


    /**
     * @return Symbol
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
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
        return $this->historyRepository->count([]);
    }

    /**
     * @param \App\Core\Domain\Model\TicTacToe\Game\Player $player
     * @param Tile $tile
     * @param Game $game
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveTurn(Player $player, Tile $tile, Game $game): void
    {
        $entity = new \App\Entity\History();
        $entity->setPlayerUuid($player->getUuid());
        $entity->setPlayerSymbol($player->symbol()->value());
        $entity->setGameUuid($game->uuid());
        $entity->setCreatedAt((string) \microtime(true));
        $entity->setTile([$tile->row(), $tile->column()]);
        $this->historyRepository->save($entity);
    }

    /**
     * @param Game $game
     * @param \App\Entity\History|null $entity
     * @return HistoryItem
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\NotAllowedSymbolValue
     * @throws \App\Core\Domain\Model\TicTacToe\Exception\OutOfLegalSizeException
     */
    private function createHistoryItem(Game $game, ?\App\Entity\History $entity): ?HistoryItem
    {
        if(null === $entity){
            return null;
        }
        $player = new Player(new Symbol($entity->getPlayerSymbol()), $entity->getPlayerUuid());
        $tile = new Tile($entity->getTile()[0], $entity->getTile()[1]);

        $historyItem = new HistoryItem($player, $tile, $game);

        return $historyItem;
    }
}
