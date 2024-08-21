<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\PlayerInfo;
use App\UseCases\Admin\ApiFootballRepositoryInterface;


class UpdateApiPlayerIds
{
    public function __construct(private ApiFootballRepositoryInterface $repository)
    {
        
    }

    public function execute()
    {
        try {
            $data = $this->repository->fetchSquads()->upsert();

            DB::transaction(function () use ($data) {
                $unique = PlayerInfo::UPSERT_UNIQUE;
                
                PlayerInfo::upsert($data, $unique, PlayerInfo::UPSERT_API_FOOTBALL_COLUMNS);
            });

        } catch (Exception $e) {
            throw $e;
        }
    }
}