<div class="w-full p-3 bg-gray-400 rounded-3xl">
    @props(['playerIcon' => 'w-28 h-28 rounded-3xl'])
    
    @if($player)
        <div class="flex justify-center">
            @if ($player->img)
                <img src="data:image/png;base64,<?= $player->img ?>" class="{{ $playerIcon }}">
            @endif
        
            @unless($player->img)
                <div class="{{ $playerIcon }} bg-gray-400"></div>
            @endunless

            <div class="flex items-center justify-center w-full">
                <p class="font-bold text-gray-100 detail__player_name whitespace-nowrap">
                    {{ $player->name }}
                </p>
            </div>
        </div>

        <livewire:rating :playerId="$player->id" :rating="$player->rating" :key="$player->id" />
    @endif
</div>