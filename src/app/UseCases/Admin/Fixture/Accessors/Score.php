<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;

use Illuminate\Support\Collection;

class Score
{
    private function __construct(private Collection $score)
    {
        
    }

    public static function create(Collection $data): self
    {
        $score = $data->dataGet('score')->except('halftime');

        return new self($score);
    }
    
    public static function reconstruct(Collection $score): self
    {
        return new self($score);
    }

    public function toModel(): Collection
    {
        return $this->score;
    }
}