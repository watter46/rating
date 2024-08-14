<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;


class PlayerName
{
    public function __construct(private Collection $names)
    {
        //
    }

    public static function create(string $name): self
    {
        $transliterated = Str::ascii($name);
        
        return new self(Str::of($transliterated)->explode(' ')); 
    }

    public function isShorten(): bool
    {
        return preg_match('/^[A-Z]\.$/', $this->getFirstName()) === 1;
    }

    public function getFullName(): string
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    public function getShortenName(): string
    {
        $shortenFirstName = Str::substr($this->getFirstName(), 0, 1);

        return $shortenFirstName.'. '.$this->getLastName();
    }

    public function getFirstName(): string
    {
        return $this->names->first();
    }

    public function getLastName(): string
    {
        return $this->names->last();
    }
}