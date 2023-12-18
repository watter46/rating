<div class="w-full h-full p-3 mt-3 bg-sky-950 rounded-3xl">
    
    @if($player)
        <div class="relative flex items-center">
            <x-rating.player-image :img="$player->img" />

            <div class="absolute w-full text-center">
                <p class="font-bold text-gray-100 detail__player_name whitespace-nowrap">
                    {{ $player->name }}
                </p>
            </div>
        </div>

        <livewire:rating :playerId="$player->id" :rating="$player->rating" :key="$player->id" />
    @endif

    @vite(['resources/css/rating.css'])
</div>