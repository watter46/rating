<x-app-layout>
    <div class="flex flex-col items-stretch w-screen p-0 pb-10 m-0">
        {{-- Score --}}
        <x-score.score
            :fixture="$fixture"
            :teams="$teams"
            :league="$league"
            :score="$score" />

        <div class="flex items-center justify-between px-5 gap-x-3">
            {{-- RatedResult --}}
            <livewire:lineups.rated-result
                :$fixture
                :$teams
                :$league
                :$score
                :$lineups
                :$fixtureId />

            @if($canRate)
                {{-- RateAllPlayers --}}
                <livewire:lineups.rate-all
                    :$lineups
                    :$fixtureId />
            @endif
        </div>
            
        <div class="flex flex-col w-full p-2">
            {{-- StartXI --}}
            <div class="flex items-center justify-center h-full">
                <div class="relative w-full h-full">
                    {{-- Field --}}
                    <div class="flex justify-center w-full">
                        <x-svg.field-image
                            id="fixture-field"
                            class="flex-grow hidden initial-state field" />

                        {{-- Players --}}
                        <div id="box"
                            class="absolute flex items-end field justify-center flex-grow w-full h-full scale-[0.92]">
                            <div class="absolute top-0 flex flex-col w-full h-full">
                                @foreach($lineups['startXI'] as $line => $players)
                                    <div id="line-{{ $line + 1 }}"
                                        class="flex items-stretch w-full h-full justify-evenly">
                                        @foreach($players as $player)
                                            <div class="flex justify-center items-center
                                                {{ count($players) === 1 ? 'w-full' : 'w-1/'.count($players) }}">
                                                <livewire:lineups.player
                                                    name="startXI"
                                                    size="w-[40px] h-[40px] md:w-20 md:h-20"
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

            <div class="flex justify-center w-full py-3 my-5 sm:mt-10">
                <div class="flex items-center justify-between w-2/3">
                    <div class="flex items-center justify-center gap-x-3">
                        {{-- RatedCount --}}
                        <livewire:lineups.rated-count :$fixtureId :$playerCount />
                    </div>
        
                    <div class="flex items-center justify-center gap-x-3">
                        {{-- ToggleUserMacine --}}
                        <livewire:lineups.toggle-user-machine />
                    </div>
                </div>
            </div>

            {{-- SubStitutes --}}
            <div class="grid w-full grid-cols-6 gap-5 md:gap-10 justify-items-center scale-[0.92]">
                @foreach($lineups['substitutes'] as $index => $substitutes)
                    @if($loop->odd)
                        @foreach($substitutes as $key => $player)
                            <div class="flex justify-center w-full col-span-2">
                                <livewire:lineups.player
                                    name="substitutes"
                                    size="w-[40px] h-[40px] md:w-20 md:h-20"
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
                                <livewire:lineups.player
                                    name="substitutes"
                                    size="w-[40px] h-[40px] md:w-20 md:h-20"
                                    :$fixtureId
                                    :$player
                                    :key="$player['id']" />
                            </div>
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>
        
        {{-- PlayerDetail --}}
        <livewire:rating.player-detail
            :$fixtureId
            :$lineups />
    </div>

    @vite(['resources/css/field.css', 'resources/js/field.js'])
</x-app-layout>