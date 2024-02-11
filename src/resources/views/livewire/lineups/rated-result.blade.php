<div>
    <div class="flex flex-col justify-center" title="RateAll" wire:click="$toggle('isOpen')">
        <x-svg.photo-image class="w-8 h-8 cursor-pointer" />
        <p class="pt-2 text-xs font-black text-center text-gray-400">Result</p>
    </div>

    @if($isOpen)
        <div class="fixed top-0 left-0 z-[99] flex items-center justify-center w-full h-full px-2 py-3"
            style="background: rgba(31, 41, 55, 0.95);">
            <div class="relative flex flex-col w-full h-full pb-5 bg-gray-900 border border-gray-700 rounded-lg">
                <div class="flex justify-end w-full p-2">
                    <div class="border-gray-400 rounded-full cursor-pointer opacity-30 hover:opacity-100 hover:border"
                        wire:click="$toggle('isOpen')">
                        <x-svg.cross-image class="w-10 h-10 fill-gray-400" />
                    </div>
                </div>

                <div class="w-full">
                    <x-result.score
                        :fixture="$fixture"
                        :teams="$teams"
                        :score="$score" />
                </div>

                {{-- StartingXI --}}
                <div class="flex items-center justify-center">
                    <div class="relative w-full">
                        {{-- Field --}}
                        <div class="scale-90 -translate-y-5">
                            <x-svg.field-image
                                id="result-field"
                                class="initial-state tilted-state" />

                            {{-- Players --}}
                            <div id="box" class="absolute flex items-end justify-center w-full h-full top-5">
                                <div class="flex flex-col w-full h-full">
                                    @foreach($lineups['startXI'] as $line => $players)
                                        <div id="line-{{ $line + 1 }}"
                                            class="flex items-stretch w-full h-full justify-evenly">
                                            @foreach($players as $player)
                                                <div class="flex justify-center items-center
                                                    {{ count($players) === 1 ? 'w-full' : 'w-1/'.count($players) }}">
                                                    <livewire:lineups.rated-player
                                                        name="startXI"
                                                        size="w-[55px] h-[55px]"
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

                {{-- Substitutes --}}
                <div class="flex flex-col items-stretch h-full">
                    <div class="grid w-full grid-cols-6 gap-5 justify-items-center">
                        @foreach($lineups['substitutes'] as $index => $substitutes)
                            @if($loop->odd)
                                @foreach($substitutes as $key => $player)
                                    <div class="flex justify-center w-full col-span-2">
                                        <livewire:lineups.rated-player
                                            name="substitutes"
                                            size="w-[45px] h-[45px]"
                                            :$fixtureId
                                            :$player
                                            :key="$player['id']" />
                                    </div>
                                @endforeach
                            @endif
        
                            @if($loop->even)
                                @foreach($substitutes as $player)
                                    <div class="col-span-2 flex justify-center w-full
                                        @if($loop->first) col-start-2 @endif">
                                        <livewire:lineups.rated-player
                                            name="substitutes"
                                            size="w-[45px] h-[45px]"
                                            :$fixtureId
                                            :$player
                                            :key="$player['id']" />
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>