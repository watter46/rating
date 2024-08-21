<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;

use Illuminate\Support\Collection;

use App\Http\Controllers\Util\LeagueImageFile;


class League
{
    private LeagueImageFile $image;
    
    private function __construct(private Collection $league)
    {
        $this->image = new LeagueImageFile;
    }

    public static function create(Collection $data)
    {
        $league = $data->dataGet('league');
        
        return new self(collect([
            'id'     => $league['id'],
            'name'   => $league['name'],
            'season' => $league['season'],
            'round'  => $league['round']
        ]));
    }

    public static function reconstruct(Collection $league): self
    {
        return new self($league);
    }

    public function toModel(): Collection
    {
        return $this->league;
    }

    public function getLeagueId(): int
    {
        return $this->league->get('id');
    }

    public function hasImage(): bool
    {
        return $this->image->exists($this->getLeagueId());
    }
}