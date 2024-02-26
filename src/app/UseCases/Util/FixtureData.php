<?php declare(strict_types=1);

namespace App\UseCases\Util;

use App\Http\Controllers\Util\FixtureFile;
use Illuminate\Support\Collection;

use App\UseCases\Admin\Fixture\FixtureDataBuilder;


readonly class FixtureData
{
    private const FIXTURE_END_STATUS = 'Match Finished';

    public function __construct(private FixtureFile $file, private FixtureDataBuilder $fixtureDataBuilder)
    {
        
    }
    
    /**
     * ファイルが存在するか判定する
     *
     * @param  int $fixtureId
     * @return bool
     */
    public function exists(int $fixtureId): bool
    {
        return $this->file->exists($fixtureId);
    }
    
    /**
     * Fixtureファイルを取得する
     *
     * @param  int $fixtureId
     * @return Collection
     */
    public function getByFile(int $fixtureId): Collection
    {
        return $this->file->get($fixtureId);
    }
    
    /**
     * FixtureのJsonデータをファイルに保存する
     *
     * @param  int $fixtureId
     * @param  Collection $fixture
     * @return void
     */
    public function store(int $fixtureId, Collection $fixture): void
    {
        $this->file->write($fixtureId, $fixture);
    }

    /**
     * 試合が終了しているか判定する
     *
     * @param  int $fixtureData
     * @return bool
     */
    public function isFinished(int $fixtureId)
    {
        $fixtureData = $this->getByFile($fixtureId);
        
        return $fixtureData->get('fixture')->status->long === self::FIXTURE_END_STATUS;
    }
    
    /**
     * Jsonデータを加工する
     *
     * @param  Collection $fixture
     * @return Collection
     */
    public function build(Collection $fixture): Collection
    {
        return $this->fixtureDataBuilder->build($fixture);
    }
}