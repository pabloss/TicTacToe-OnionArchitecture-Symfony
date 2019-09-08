<?php
declare(strict_types=1);

namespace App\AppCore\DomainModel\Game\Player;

interface PlayerInterface
{
    public function symbolValue(): string;
    public function uuid(): string;
}
