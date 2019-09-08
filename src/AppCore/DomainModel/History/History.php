<?php
declare(strict_types=1);

namespace App\AppCore\DomainModel\History;

use App\AppCore\DomainModel\Game\Board\TileInterface;
use App\AppCore\DomainModel\Game\Exception\NotAllowedSymbolValue;
use App\AppCore\DomainModel\Game\GameInterface;
use App\AppCore\DomainModel\Game\Player\PlayerInterface;
use App\AppCore\DomainModel\Game\Player\Symbol;

/**
 * Class History
 * @package App\Presentation\Web\Pub\Service\History
 */
class History implements HistoryInterface
{
    const LIMIT = 9;

    /** @var HistoryRepositoryInterface */
    private $historyRepository;

    /**
     * History constructor.
     * @param HistoryRepositoryInterface $historyRepository
     */
    public function __construct(HistoryRepositoryInterface $historyRepository)
    {
        $this->historyRepository = $historyRepository;
    }

    /**
     * @param GameInterface $game
     * @return HistoryContent
     */
    public function content(GameInterface $game): HistoryContent
    {
        return $this->historyRepository->getByGame($game);
    }

    public function lastItemPlayerSymbolValue(GameInterface $game): ?string
    {
        return (null !== $this->lastItem($game)) ? $this->lastItem($game)->player()->symbol()->value(): null;
    }

    /**
     * @param GameInterface $game
     * @return HistoryItem|null
     */
    public function lastItem(GameInterface $game): ?HistoryItem
    {
        if (0 === (int)$this->historyRepository->count(['gameUuid' => $game->uuid()])) {
            return null;
        }
        return $this->historyRepository->getLastByGame($game);
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
     * @param GameInterface $game
     * @return int
     */
    public function length(GameInterface $game): int
    {
        return $this->historyRepository->count(['gameUuid' => $game->uuid()]);
    }

    /**
     * @param PlayerInterface $player
     * @param TileInterface $tile
     * @param GameInterface $game
     */
    public function saveTurn(PlayerInterface $player, TileInterface $tile, GameInterface $game): void
    {
        $this->historyRepository->save(new HistoryItem($player, $tile, $game));
    }
}
