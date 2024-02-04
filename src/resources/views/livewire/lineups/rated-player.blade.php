<div id="result-{{ $name }}" class="text-center"
    x-data="{
        rating: @entangle('rating'),
        machine: @entangle('defaultRating')
    }"
    wire:ignore.self>
    
    <div class="flex justify-center">
        <div class="relative flex justify-center place-items-center">
            {{-- PlayerImage --}}
            <x-player.player-image
                class="w-16 h-16"
                :number="$player['number']"
                :img="$player['img']" />

            {{-- Goals --}}
            <div class="absolute top-0 left-0 -translate-x-1/3">
                <x-player.goals :goals="$player['goal']" />
            </div>

            {{-- Assists --}}
            <div class="absolute top-0 right-0 translate-x-1/3">
                <x-player.assists :assists="$player['assists']" />
            </div>
            
            {{-- Rating --}}
            <div class="text-sm font-black text-gray-50">
                <div class="absolute bottom-0 right-0  translate-x-1/2 w-[45px]">
                    @if($mom)
                        <div class="flex items-center justify-center px-5 py-0.5 gap-x-0.5 rounded-xl" style="background-color: #0E87E0">
                            <p class="text-xs">★</p>
                            <p x-text="ratingValue(rating)"></p>
                        </div>
                    @endif

                    @unless($mom)
                        <div class="flex justify-center px-5 py-0.5 rounded-xl"
                            :style="`background-color: ${ratingBgColor(rating)}`">
                            <p x-text="ratingValue(rating)"></p>
                        </div>
                    @endunless
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-center font-black text-white gap-x-1">
        <p>{{ $player['number'] }}</p>
    
        <p>{{ $this->toLastName() }}</p>
    </div>
    
    @vite(['resources/css/player.css', 'resources/js/rating.js'])
</div>