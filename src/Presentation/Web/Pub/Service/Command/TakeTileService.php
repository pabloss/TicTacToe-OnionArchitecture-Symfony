<?php
declare(strict_types=1);

namespace App\Presentation\Web\Pub\Service\Command;

use App\AppCore\ApplicationServices\TakeTileService as CoreTakeTileService;
use App\AppCore\DomainModel\Game\Board\Tile;
use App\AppCore\DomainModel\Game\Exception\NotAllowedSymbolValue;
use App\AppCore\DomainModel\Game\Exception\OutOfLegalSizeException;
use App\AppCore\DomainModel\Game\Player\Player;
use App\AppCore\DomainModel\Game\Player\Symbol;

class TakeTileService
{
    /** @var CoreTakeTileService  */
    private $domainService;

    /**
     * TakeTileService constructor.
     * @param CoreTakeTileService $domainService
     */
    public function __construct(CoreTakeTileService $domainService)
    {
        $this->domainService = $domainService;
    }

    /**
     * @param string $symbol
     * @param string $uuid
     * @param int $x
     * @param int $y
     * @throws NotAllowedSymbolValue
     * @throws OutOfLegalSizeException
     */
    public function takeTile(string $symbol, string $uuid, int $x, int $y)
    {
        $this->domainService->takeTile(new Player(new Symbol($symbol), $uuid), new Tile($x, $y));
    }
}
