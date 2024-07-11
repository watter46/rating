<?php declare(strict_types=1);

namespace App\UseCases\Admin\Data\FlashLiveSports;

use Illuminate\Support\Collection;

class PlayerData
{
    public function __construct(private Collection $playerData)
    {
        //
    }

    public static function create(Collection $playerData)
    {
        dd($playerData);
        return new self($playerData);
    }

    public function get(): Collection
    {
        return $this->playerData;
    }
}