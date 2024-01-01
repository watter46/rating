<div id="player" class="hidden w-full text-center" wire:ignore.self
    x-data="{
        rating: @entangle('rating')
    }">
    <div class="flex justify-center">
        <div class="relative" wire:click="toDetail('{{ $player['id'] }}')" class="player">
            <x-rating.player-image
                :number="$player['number']"
                :img="$player['img']"
                type="field" />
            
            <div class="absolute bottom-0 px-2 border-2 border-gray-200 rounded-lg -end-7"
                :style="`background-color: ${ratingBgColor(rating)}`">
                <p class="text-xl font-black text-gray-200" x-text="paddingZero(rating)"></p>
            </div>
        </div>
    </div>

    <p class="font-black text-white whitespace-nowrap">{{ $player['name'] }}</p>

    @vite(['resources/css/player.css', 'resources/js/rating.js'])
</div>