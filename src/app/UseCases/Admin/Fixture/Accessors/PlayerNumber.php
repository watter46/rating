<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;


class PlayerNumber
{
    private function __construct(private ?int $number)
    {
        
    }

    public static function create(?int $number)
    {
        return new self($number);
    }

    public function get(): ?int
    {
        return $this->number;
    }

    public function update(int $newNumber): self
    {
        return new self($newNumber);
    }

    public function equal(PlayerNumber $number)
    {
        return $this->number === $number->get();
    }
}