<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors\Api;

use Illuminate\Support\Collection;

use App\UseCases\Admin\Fixture\Accessors\PlayerName;
use App\UseCases\Admin\Fixture\Accessors\PlayerNumber;


class ApiPlayer
{
    private function __construct(
        private int $id,
        private PlayerName $name,
        private PlayerNumber $number
    ) {

    }

    public static function create(Collection $player)
    {
        return new self(
            $player['id'],
            PlayerName::create($player['name']),
            PlayerNumber::create($player['number'])
        );
    }

    public function equal(int $id)
    {
        return $this->id === $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNumber()
    {
        return $this->number;
    }
}