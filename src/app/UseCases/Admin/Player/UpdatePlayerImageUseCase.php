<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player;

use App\Http\Controllers\Util\PlayerImageFile;
use App\Models\PlayerInfo;
use App\UseCases\Admin\SofaScoreRepositoryInterface;
use App\UseCases\Api\SofaScore\PlayerImageFetcher;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


final readonly class UpdatePlayerImageUseCase
{
    public function __construct(
        private SofaScoreRepositoryInterface $sofaScoreRepository,
        private PlayerImageFile $file
    ) {
        //
    }

    public function execute(string $playerInfoId): void
    {
        try {
            /** @var PlayerInfo $playerInfo */
            $playerInfo = PlayerInfo::find($playerInfoId);

            if (!$playerInfo->sofa_player_id) {
                throw new Exception('Invalid SofaId');
            }

            $playerImage = $this->sofaScoreRepository->fetchPlayerImage($playerInfo->sofa_player_id);
                    
            $this->file->write($playerInfo->foot_player_id, $playerImage);

        } catch (ClientException $e) {
            throw new ModelNotFoundException('SofaScore: Player Not Found.');
              
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('PlayerInfo Not Found.');

        } catch (Exception $e) {
            throw $e;
        }
    }
}