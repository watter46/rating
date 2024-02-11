<x-app-layout>
    <div class="flex flex-col items-stretch pb-10">
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
            <div class="flex items-center justify-center">
                <div class="relative w-full">
                    {{-- Field --}}
                    <div class="scale-x-[0.95] -translate-y-5">
                        <x-svg.field-image
                            id="fixture-field"
                            class="hidden initial-state titled-state" />

                        {{-- Players --}}
                        <div id="box" class="absolute flex items-end justify-center w-full h-full top-10">
                            <div class="flex flex-col w-full h-full">
                                @foreach($lineups['startXI'] as $line => $players)
                                    <div id="line-{{ $line + 1 }}"
                                        class="flex items-stretch w-full h-full justify-evenly">
                                        @foreach($players as $player)
                                            <div class="flex justify-center items-center
                                                {{ count($players) === 1 ? 'w-full' : 'w-1/'.count($players) }}">
                                                <livewire:lineups.player
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

                    <div class="absolute flex items-center justify-center font-black left-10 bottom-14 gap-x-3"
                        x-cloak>
                        {{-- RatedCount --}}
                        <livewire:lineups.rated-count :$fixtureId :$playerCount />
                    </div>

                    <div class="absolute flex items-center justify-center bottom-14 right-10 gap-x-3"
                        x-cloak>
                        {{-- ToggleUserMacine --}}
                        <livewire:lineups.toggle-user-machine />
                    </div>
                </div>
            </div>

            {{-- SubStitutes --}}
            <div class="grid w-full grid-cols-6 gap-5 justify-items-center">
                @foreach($lineups['substitutes'] as $index => $substitutes)
                    @if($loop->odd)
                        @foreach($substitutes as $key => $player)
                            <div class="flex justify-center w-full col-span-2">
                                <livewire:lineups.player
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
                                <livewire:lineups.player
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
        
        {{-- PlayerDetail --}}
        <livewire:rating.player-detail
            :$fixtureId
            :$lineups />
    </div>

    @vite(['resources/css/field.css', 'resources/js/field.js'])
</x-app-layout>