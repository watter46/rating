<?php declare(strict_types=1);

namespace App\Livewire;

enum MessageType
{
    case Success;
    case Error;
    
    /**
     * toArray
     *
     * @param  string $text
     * @return array
     */
    public function toArray(string $text): array
    {
        return [
            'type' => $this->name,
            'text' => $text
        ];
    }
}
