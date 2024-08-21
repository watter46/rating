<?php declare(strict_types=1);

namespace App\UseCases\Admin\UsersPlayerRating;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\UsersPlayerRating as UsersPlayerRatingModel;
use App\UseCases\Admin\UsersPlayerRating\Accessors\UsersPlayerRating;


class UpdateUsersPlayerRating
{
    public function __construct(private UsersPlayerRating $usersPlayerRating)
    {
        //
    }

    /**
     * 指定の試合のユーザー全体の平均評価点を保存する
     *
     * @return void
     */
    public function execute(string $fixtureInfoId)
    {
        try {
            $data = $this->usersPlayerRating->upsert($fixtureInfoId);
            
            DB::transaction(function () use ($data) {                
                UsersPlayerRatingModel::upsert(
                    $data,
                    UsersPlayerRatingModel::UPSERT_UNIQUE
                );
            });

        } catch (Exception $e) {
            throw $e;
        }
    }
}