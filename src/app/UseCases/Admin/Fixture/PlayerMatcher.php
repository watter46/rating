<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use App\UseCases\Admin\Fixture\Accessors\PlayerName;
use App\UseCases\Admin\Fixture\Accessors\PlayerNumber;


class PlayerMatcher
{
    public function __construct(private PlayerName $name, private PlayerNumber $number)
    {
        
    }
        
    /**
     * match
     *
     * @param  array{id: string, name: string, number: int} $player
     * @return bool
     */
    public function match(array $player): bool
    {
        return collect([
            $this->matchShortenNameAndNumber($player),
                $this->matchFullNameAndNumber($player),
                $this->matchShortenNameAndNumber($player),
                $this->matchLastNameAndNumber($player)
            ])
            ->some(fn($matched) => $matched);
    }
    
    /**
     * matchFullNameAndNumber
     *
     * @param  array{
     *  flash_id: string,
     *  name: PlayerName,
     *  number: PlayerNumber,
     *  flash_image_id: ?string
     * } $player
     * @return bool
     */
    private function matchFullNameAndNumber(array $player): bool
    {
        return $this->name->equalsFullName($player['name']) && $this->number->equal($player['number']);
    }

    /**
     * matchFullNameAndNumber
     *
     * @param  array{
     *  flash_id: string,
     *  name: PlayerName,
     *  number: PlayerNumber,
     *  flash_image_id: ?string
     * } $player
     * @return bool
     */
    private function matchShortenNameAndNumber(array $player): bool
    {
        return $this->name->equalsShortenName($player['name'])
            && $this->number->equal($player['number']);
    }

    /**
     * matchFullNameAndNumber
     *
     * @param  array{
     *  flash_id: string,
     *  name: PlayerName,
     *  number: PlayerNumber,
     *  flash_image_id: ?string
     * } $player
     * @return bool
     */
    private function matchLastNameAndNumber($player): bool
    {
        return $this->name->equalsLastName($player['name'])
            && $this->number->equal($player['number']);
    }
}
