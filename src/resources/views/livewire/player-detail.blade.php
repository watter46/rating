<div class="relative w-screen">
    <div class="absolute inset-y-0 right-0 flex items-center justify-center w-1/2 h-screen">
        <div class="w-5/6 bg-gray-400 rounded-3xl">
            <div class="p-5">                
                @props(['playerIcon' => 'w-32 h-32 rounded-3xl'])
                
                @if($player)
                    <div class="flex justify-center">
                        @if ($player->img)
                            <img src="data:image/png;base64,<?= $player->img ?>" class="{{ $playerIcon }}">
                        @endif
                    
                        @unless($player->img)
                            <div class="{{ $playerIcon }} bg-gray-400"></div>
                        @endunless

                        <div class="flex items-center justify-center w-full">
                            <p class="text-5xl font-bold text-gray-100 whitespace-nowrap">{{ $player->name }}</p>
                        </div>
                    </div>

                    <x-field.rating :playerId="$player->id" :rating="6.6" />
                @endif
            </div>
        </div>
    </div>
</div>