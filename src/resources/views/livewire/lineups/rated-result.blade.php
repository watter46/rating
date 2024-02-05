<div>
    <div class="flex flex-col items-center justify-center cursor-pointer" wire:click="$toggle('isOpen')">
        <p class="text-xs text-gray-300">Result</p>
        <x-svg.photo-image />
    </div>

    @if($isOpen)
        <div class="fixed top-0 left-0 z-[99] flex items-center justify-center w-full h-screen px-10 py-5"
            style="background: rgba(31, 41, 55, 0.95);">
            <div class="relative flex w-full h-full bg-gray-800 border border-gray-700 rounded-lg">
                <div class="flex justify-center w-full h-full">
                    {{-- Substitutes --}}
                    <div class="grid content-center h-full gap-y-3">
                        @foreach($lineups['substitutes'] as $player)
                            {{-- RatedPlayer --}}
                            <livewire:lineups.rated-player
                                name="substitutes"
                                :$fixtureId
                                :$player
                                :key="$player['id']" />
                        @endforeach
                    </div>

                    <div class="flex h-full mx-5 justify-evenly">
                        {{-- StartingXI --}}
                        <div class="relative h-full -top-5">
                            <div class="flex justify-center h-full">
                                {{-- Field --}}
                                <x-svg.field-image
                                    id="result-field"
                                    class="w-full h-full tilted-state" />

                                {{-- Players --}}
                                <div id="box" class="absolute flex items-end justify-center w-full h-full">
                                    <div class="flex flex-col w-full h-full pt-10">
                                        @foreach($lineups['startXI'] as $line => $players)
                                            <div id="line-{{ $line + 1 }}"
                                                class="flex items-stretch w-full h-full justify-evenly">
                                                @foreach($players as $player)
                                                    <div class="flex justify-center items-center
                                                        {{ count($players) === 1 ? 'w-full' : 'w-1/'.count($players) }}">
                                                        <livewire:rated-player
                                                            name="startXI"
                                                            :$fixtureId
                                                            :$player
                                                            :key="$player['id']" />
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="absolute top-0 right-0 p-2 border-gray-400 rounded-full cursor-pointer hover:border"
                    wire:click="$toggle('isOpen')">
                    <x-svg.cross-image class="w-10 h-10 fill-gray-400" />
                </div>
            </div>
        </div>
    @endif
</div>