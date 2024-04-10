<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Illuminate\Support\Collection;

interface ValidatorInterface
{    
    /**
     * 保存されていないチームIDを取得する
     *
     * @return Collection<int>
     */
    public function getInvalidTeamIds(): Collection;

    /**
     * 保存されていないリーグIDを取得する
     *
     * @return Collection<int>
     */
    public function getInvalidLeagueIds(): Collection;
    
    /**
     * データがすべて存在しているか判定する
     *
     * @return bool
     */
    public function checkRequiredData(): bool;
}