<?php declare(strict_types=1);

namespace App\Livewire\User\Data;

enum ChunkType: int
{
    case BigChunk = 3;
    case SmallChunk = 2;

    private function isBigChunk(): bool
    {
        return $this === self::BigChunk;
    }

    public function changeSize(): self
    {
        return $this->isBigChunk() ? self::SmallChunk : self::BigChunk;
    }
}