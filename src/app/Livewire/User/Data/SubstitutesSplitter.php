<?php declare(strict_types=1);

namespace App\Livewire\User\Data;

use Illuminate\Support\Collection;


readonly class SubstitutesSplitter
{    
    private Collection $result;

    public function __construct(private Collection $substitutes)
    {
        $this->chunk($substitutes->values(), collect());
    }

    public static function split(Collection $substitutes)
    {
        return new self($substitutes);
    }

    private function chunk(
        Collection $substitutes,
        Collection $result = new Collection(),
        ChunkType $chunk = ChunkType::BigChunk)
    {
        if ($substitutes->isEmpty()) {
            return $this->result = $result;
        }

        $remainingItems = $substitutes->splice($chunk->value);
        $resultItems    = $result->push($substitutes);

        $this->chunk($remainingItems, $resultItems, $chunk->changeSize());
    }

    public function get(): Collection
    {
        return $this->result;
    }
}