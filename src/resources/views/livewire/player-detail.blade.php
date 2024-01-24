<div class="w-full h-full p-3 mt-3 bg-sky-950 rounded-3xl">
    @if($player)
        <div class="relative flex items-center">
            <x-rating.player-image
                :isEvaluated="false"
                :number="$player['number']"
                :img="$player['img']"
                type="rating" />

            <div class="absolute flex justify-center w-full font-bold text-center text-gray-100 gap-x-3">                    
                <p class="detail__player_name whitespace-nowrap">
                    {{ $this->toLastName() }}
                </p> 
            </div>
        </div>

        <div class="flex w-full mt-3 font-black text-center justify-evenly gap-x-3">
            {{-- Position --}}
            <div class="p-2">
                <p class="text-gray-500">Position</p>
                <p class="text-xl text-gray-300">{{ $player['position'] }}</p>
            </div>

            {{-- ShirtNumber --}}
            <div class="p-2">
                <p class="text-gray-500">ShirtNumber</p>
                <p class="text-xl text-gray-300">{{ $player['number'] }}</p>
            </div>

            {{-- machineRating --}}
            <div class="p-2">
                <p class="text-gray-500">machineRating</p>
                <p class="text-xl text-gray-300">{{ $player['defaultRating'] }}</p>
            </div>
            
            {{-- Goals --}}
            <div class="flex flex-col items-center p-2">
                <p class="text-gray-500">Goals</p>
                <div class="flex items-center justify-center w-full h-full">
                    <x-player.goals :goals="$player['goal']" />
                </div>
            </div>
            
            {{-- Assists --}}
            <div class="flex flex-col items-center p-2">
                <p class="text-gray-500">Assists</p>
                <div class="flex items-center justify-center w-full h-full">
                    <x-player.assists :assists="$player['assists']" />
                </div>
            </div>
        </div>
        
        {{-- Rating --}}
        <livewire:rating
            :$fixtureId
            :playerId="$player['id']"
            :defaultRating="$player['defaultRating']"
            :key="$player['id']" />
    @endif

    @vite(['resources/css/rating.css'])
</div>