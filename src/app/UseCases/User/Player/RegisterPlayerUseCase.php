<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use App\Models\PlayerInfo;
use App\Http\Controllers\Util\PlayerFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\UseCases\Admin\Player\Player;

final readonly class RegisterPlayerUseCase
{
    public function __construct(private RegisterPlayerBuilder $builder)
    {
        //
    }

    public function execute(Collection $invalidPlayers)
    {
        try {
            $data = $this->builder->build($invalidPlayers);

            DB::transaction(function () use ($data) {
                $unique = ['id'];
                $updateColumns = ['name', 'number', 'season', 'foot_player_id', 'sofa_player_id'];
                
                PlayerInfo::upsert($data, $unique, $updateColumns);
            });

        } catch (Exception $e) {
            throw $e;
        }
    }
}