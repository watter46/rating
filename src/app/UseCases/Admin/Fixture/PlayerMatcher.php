<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use App\UseCases\Admin\Fixture\Accessors\Flash\FlashPlayer;
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
     * @param  FlashPlayer $flashPlayer
     * @return bool
     */
    public function match(FlashPlayer $flashPlayer): bool
    {
        return collect([
            $this->matchShortenNameAndNumber($flashPlayer),
                $this->matchFullNameAndNumber($flashPlayer),
                $this->matchShortenNameAndNumber($flashPlayer),
                $this->matchLastNameAndNumber($flashPlayer)
            ])
            ->some(fn($matched) => $matched);
    }
    
    /**
     * matchFullNameAndNumber
     *
     * @param  FlashPlayer $flashPlayer
     * @return bool
     */
    private function matchFullNameAndNumber(FlashPlayer $flashPlayer): bool
    {
        return $this->name->equalsFullName($flashPlayer->getName()) && $this->number->equal($flashPlayer->getNumber());
    }

    /**
     * matchFullNameAndNumber
     *
     * @param  FlashPlayer $flashPlayer
     * @return bool
     */
    private function matchShortenNameAndNumber(FlashPlayer $flashPlayer): bool
    {
        return $this->name->equalsShortenName($flashPlayer->getName())
            && $this->number->equal($flashPlayer->getNumber());
    }

    /**
     * matchFullNameAndNumber
     *
     * @param  FlashPlayer $flashPlayer
     * @return bool
     */
    private function matchLastNameAndNumber(FlashPlayer $flashPlayer): bool
    {
        return $this->name->equalsLastName($flashPlayer->getName())
            && $this->number->equal($flashPlayer->getNumber());
    }


    // /**
    //  * match
    //  *
    //  * @param  array{id: string, name: string, number: int} $player
    //  * @return bool
    //  */
    // public function match(array $player): bool
    // {
    //     return collect([
    //         $this->matchShortenNameAndNumber($player),
    //             $this->matchFullNameAndNumber($player),
    //             $this->matchShortenNameAndNumber($player),
    //             $this->matchLastNameAndNumber($player)
    //         ])
    //         ->some(fn($matched) => $matched);
    // }
    
    // /**
    //  * matchFullNameAndNumber
    //  *
    //  * @param  array{
    //  *  flash_id: string,
    //  *  name: PlayerName,
    //  *  number: PlayerNumber,
    //  *  flash_image_id: ?string
    //  * } $player
    //  * @return bool
    //  */
    // private function matchFullNameAndNumber(array $player): bool
    // {
    //     return $this->name->equalsFullName($player['name']) && $this->number->equal($player['number']);
    // }

    // /**
    //  * matchFullNameAndNumber
    //  *
    //  * @param  array{
    //  *  flash_id: string,
    //  *  name: PlayerName,
    //  *  number: PlayerNumber,
    //  *  flash_image_id: ?string
    //  * } $player
    //  * @return bool
    //  */
    // private function matchShortenNameAndNumber(array $player): bool
    // {
    //     return $this->name->equalsShortenName($player['name'])
    //         && $this->number->equal($player['number']);
    // }

    // /**
    //  * matchFullNameAndNumber
    //  *
    //  * @param  array{
    //  *  flash_id: string,
    //  *  name: PlayerName,
    //  *  number: PlayerNumber,
    //  *  flash_image_id: ?string
    //  * } $player
    //  * @return bool
    //  */
    // private function matchLastNameAndNumber($player): bool
    // {
    //     return $this->name->equalsLastName($player['name'])
    //         && $this->number->equal($player['number']);
    // }
}
