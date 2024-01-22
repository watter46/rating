<div id="{{ $name }}" class="hidden text-center"
    x-data="{
        rating: @entangle('rating')
    }"
    wire:ignore.self>
    
    <div class="flex justify-center" wire:click="toDetail('{{ $player['id'] }}')" class="player">
        <div class="relative flex justify-center w-fit place-items-center">
            {{-- PlayerImage --}}
            <x-rating.player-image
                :number="$player['number']"
                :img="$player['img']"
                :isEvaluated="$isEvaluated"
                type="field" />

            {{-- Goals --}}
            <div class="absolute top-0 left-0 -translate-x-1/3">
                <x-player.goals :goals="$player['goal']" />
            </div>

            {{-- Assists --}}
            <div class="absolute top-0 right-0 translate-x-1/3">
                <x-player.assists :assists="$player['assists']" />
            </div>
            
            {{-- Rating --}}
            <div class="absolute bottom-0 right-0 w-10 text-center translate-x-1/2 rounded-xl"
                :style="`background-color: ${ratingBgColor(rating)}`">
                <p class="font-black text-gray-200" x-text="ratingValue(rating)"></p>
            </div> 
        </div>
    </div>

    <div class="flex justify-center font-black text-white break-all gap-x-1">
        <p>{{ $player['number'] }}</p>
    
        <p>{{ $this->toLastName() }}</p>
    </div>
    
    @vite(['resources/css/player.css', 'resources/js/rating.js'])
</div>