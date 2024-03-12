<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\UpdatePlayerInfos;

use Illuminate\Support\Str;


class PlayerMatcher
{
    public function isMatch(array $playerData, array $targetPlayerData)
    {
        if ($this->isNameMatch($playerData, $targetPlayerData)) {
            return true;
        }
        
        if ($this->isNumberMatch($playerData, $targetPlayerData)) {
            return true;
        }

        return false;
    }
    
    /**
     * プレイヤーの背番号が一致するか判定する
     *
     * @param  array $playerData
     * @param  array $targetPlayerData
     * @return bool
     */
    private function isNumberMatch($playerData, $targetPlayerData): bool
    {
        if (!$playerData['number']) {
            return false;
        }

        return $playerData['number'] === $targetPlayerData['number'];
    }
    
    /**
     * プレイヤーの名前が一致するか判定する
     *
     * @param  array $playerData
     * @param  array $targetPlayerData
     * @return bool
     */
    private function isNameMatch($playerData, $targetPlayerData): bool
    {
        $sofa_name = Str::after($playerData['name'], ' ');
        $foot_name = Str::after($targetPlayerData['name'], ' ');

        return $sofa_name === $foot_name;
    }
}