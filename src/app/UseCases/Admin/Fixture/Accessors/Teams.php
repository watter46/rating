<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;

use Illuminate\Support\Collection;

use App\Http\Controllers\Util\TeamImageFile;


class Teams
{
    private TeamImageFile $image;
    
    private function __construct(private Collection $teams)
    {
        $this->image = new TeamImageFile;
    }

    public static function create(Collection $data): self
    {
        $teams = $data
            ->dataGet('teams')
            ->map(function (Collection $team) {
                return [
                    'id'     => $team['id'],
                    'name'   => $team['name'],
                    'winner' => $team['winner']
                ];
            });

        return new self($teams);
    }

    public static function reconstruct(Collection $teams): self
    {
        return new self($teams);
    }

    public function toModel(): Collection
    {
        return $this->teams;
    }

    public function hasImages(): bool
    {
        return $this->teams
            ->pluck('id')
            ->every(function (int $id) {
                return $this->image->exists($id);
            });
    }

    public function getInvalidImageIds(): Collection
    {
        return $this->teams
            ->pluck('id')
            ->filter(function (int $id) {
                return !$this->image->exists($id);
            });
    }
}