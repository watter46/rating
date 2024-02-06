<div class="absolute inset-0 hidden w-full min-h-screen"
    :class="{'block': open, 'hidden': !open}"
    x-data="{ open: @entangle('open') }">
        <div class="flex flex-col items-stretch justify-center w-full min-h-screen">
            @if($player)
                {{-- <div class="relative flex items-center">
                    <x-player.player-image
                        class="w-20 h-20"
                        :number="$player['number']"
                        :img="$player['img']" />

                    <div class="absolute flex justify-center w-full gap-x-3">                    
                        <p class="font-bold text-center text-gray-100 detail__player_name whitespace-nowrap">
                            {{ $this->toLastName() }}
                        </p> 
                    </div>
                </div> --}}

                <div class="flex w-full mt-3 font-black text-center justify-evenly gap-x-3">
                    {{-- Position --}}
                    {{-- <div class="p-2">
                        <p class="text-gray-500">Position</p>
                        <p class="text-xl text-gray-300">{{ $player['position'] }}</p>
                    </div> --}}

                    {{-- ShirtNumber --}}
                    {{-- <div class="p-2">
                        <p class="text-gray-500">ShirtNumber</p>
                        <p class="text-xl text-gray-300">{{ $player['number'] }}</p>
                    </div> --}}

                    {{-- machineRating --}}
                    {{-- <div class="p-2">
                        <p class="text-gray-500">MachineRating</p>
                        <p class="text-xl text-gray-300">{{ $player['defaultRating'] }}</p>
                    </div> --}}
                    
                    {{-- Goals --}}
                    {{-- <div class="flex flex-col items-center p-2">
                        <p class="text-gray-500">Goals</p>
                        <div class="flex items-center justify-center w-full h-full">
                            <x-player.goals :goals="$player['goal']" />
                        </div>
                    </div> --}}
                    
                    {{-- Assists --}}
                    {{-- <div class="flex flex-col items-center p-2">
                        <p class="text-gray-500">Assists</p>
                        <div class="flex items-center justify-center w-full h-full">
                            <x-player.assists :assists="$player['assists']" />
                        </div>
                    </div> --}}
                </div>
                
                {{-- Rating --}}
                {{-- <livewire:rating.rating
                    :$fixtureId
                    :playerId="$player['id']"
                    :defaultRating="$player['defaultRating']"
                    :key="$player['id']" /> --}}
            @endif

            {{-- Responsive --}}
            <div x-show="open" class="absolute w-full h-full">
                @if ($player)
                    <div class="relative flex items-center justify-center w-full h-screen"
                        style="background: rgba(31, 41, 55, 0.95);">
                        <div class="absolute flex flex-col items-stretch top-[10%] w-11/12 p-3 bg-sky-950 h-4/6 rounded-xl">
                            <div class="flex items-center justify-end w-full h-fit">
                                <button class="border-gray-400 rounded-full cursor-pointer hover:border"        wire:click="$toggle('open')">
                                    <x-svg.cross-image class="w-8 h-8 fill-gray-400" />
                                </button>
                            </div>

                            <div class="relative flex flex-col items-center justify-center gap-3">
                                <x-player.player-image
                                    class="w-20 h-20"
                                    :number="$player['number']"
                                    :img="$player['img']" />

                                <div class="flex justify-center w-full gap-x-3">                    
                                    <p class="text-sm font-bold text-center text-gray-100 detail__player_name whitespace-nowrap">
                                        {{ $player['number'] }}
                                    </p> 
                                    <p class="text-sm font-bold text-center text-gray-100 detail__player_name whitespace-nowrap">
                                        {{ $this->toLastName() }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid w-full grid-cols-3 mt-3 mb-3 text-center gap-x-0.5 gap-y-1">
                                {{-- Position --}}
                                <div class="p-0.5">
                                    <p class="text-xs font-black text-gray-500">Position</p>
                                    <p class="font-black text-gray-300 text-md">{{ $player['position'] }}</p>
                                </div>

                                {{-- ShirtNumber --}}
                                <div class="p-0.5">
                                    <p class="text-xs font-black text-gray-500">ShirtNumber</p>
                                    <p class="font-black text-gray-300 text-md">{{ $player['number'] }}</p>
                                </div>

                                {{-- machineRating --}}
                                <div class="p-0.5">
                                    <p class="text-xs font-black text-gray-500">MachineRating</p>
                                    <p class="font-black text-gray-300 text-md">{{ $player['defaultRating'] }}</p>
                                </div>
                                
                                {{-- Goals --}}
                                <div class="flex flex-col items-center p-0.5">
                                    <p class="text-xs font-black text-gray-500">Goals</p>
                                    <div class="flex items-center justify-center w-full h-full">
                                        <x-player.goals :goals="$player['goal']" />
                                    </div>
                                </div>
                                
                                {{-- Assists --}}
                                <div class="flex flex-col items-center p-0.5">
                                    <p class="text-xs font-black text-gray-500">Assists</p>
                                    <div class="flex items-center justify-center w-full h-full">
                                        <x-player.assists :assists="$player['assists']" />
                                    </div>
                                </div>
                            </div>

                            {{-- Rating --}}
                            <div class="flex items-center justify-center w-full h-full border-t-2 border-gray-700">
                                <livewire:rating.rating
                                    :$fixtureId
                                    :playerId="$player['id']"
                                    :defaultRating="$player['defaultRating']"
                                    :key="$player['id']" />
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    @vite(['resources/css/rating.css'])
</div>