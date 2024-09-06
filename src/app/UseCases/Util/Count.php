<?php declare(strict_types=1);

namespace App\UseCases\Util;


abstract class Count
{
    protected int $count;
    
    private function __construct(int $count = 0)
    {
        $this->count = $count;
    }

    abstract public static function create(): static;

    public static function reconstruct(int $count)
    {
        return new static($count);
    }

    public function increment()
    {
        return new self($this->count++);
    }

    public function value()
    {
        return $this->count;
    }
}