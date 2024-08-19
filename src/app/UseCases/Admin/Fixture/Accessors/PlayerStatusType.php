<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;


enum PlayerStatusType
{
    /** 新しいプレイヤーが未登録であり保存する必要がある */
    case NeedsRegister;

    /** 既存のプレイヤー情報を更新する必要がある */
    case NeedsUpdate;

    /** FlashLiveScoreからデータを取得して保存する必要がある */
    case NeedsFetchFlash;

    /** 正常なPlayerInfo */
    case Valid;
    

    public function needsRegister(): bool
    {
        return $this === self::NeedsRegister;
    }

    public function needsUpdate(): bool
    {
        return $this === self::NeedsUpdate;
    }

    public function needsFetchFlash(): bool
    {
        return $this === self::NeedsFetchFlash;
    }
    
    public function isValid(): bool
    {
        return $this === self::Valid;
    }
}