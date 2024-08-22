<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;

use Illuminate\Support\Str;


class PlayerName
{
    public function __construct(private string $firstName, private ?string $lastName = null)
    {
        //
    }

    public static function create(string $name): self
    {
        $transliterated = Str::ascii($name);
        
        $first = Str::of($transliterated)->explode(' ')->first();
        $last  = Str::of($transliterated)->explode(' ')->last();
        
        return new self(
            $first,
            $first !== $last ? $last : null
        );
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

    private function getFirstName(): string
    {
        return $this->firstName;
    }

    private function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function equalsFullName(PlayerName $name)
    {
        return $this->getFullName() === $name->getFullName();
    }

    public function equalsShortenName(PlayerName $name)
    {
        return $this->getShortenName() === $name->getShortenName();
    }

    public function equalsLastName(PlayerName $name)
    {
        return $this->getLastName() === $name->getLastName();
    }

    public function swapFirstAndLastName(): self
    {                                
        return new self($this->lastName, $this->firstName);
    }
}