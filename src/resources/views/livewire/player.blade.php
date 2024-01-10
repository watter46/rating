<div id="{{ $name }}" class="hidden text-center"
    x-data="{
        rating: @entangle('rating')
    }"
    wire:ignore.self>
    
    <div class="relative" wire:click="toDetail('{{ $player['id'] }}')" class="player">
        <div class="flex justify-between">
            <div>
                <x-player.goals :goals="$player['goal']" />
            </div>
            
            <div>
                <x-player.assists :assists="$player['assists']" />
            </div>
        </div>
        
        <x-rating.player-image
            :number="$player['number']"
            :img="$player['img']"
            :isEvaluated="$isEvaluated"
            type="field" />
        
        <div class="absolute bottom-0 flex justify-center w-12 px-2 border-2 border-gray-200 rounded-lg -end-7"
            :style="`background-color: ${ratingBgColor(rating)}`">
            <p class="text-xl font-black text-gray-200" x-text="ratingValue(rating)"></p>
        </div> 
    </div>

    <p class="font-black text-white whitespace-nowrap">{{ $player['name'] }}</p>
    
    @vite(['resources/css/player.css', 'resources/js/rating.js'])
</div>