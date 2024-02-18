<x-app-layout>
    <div class="w-full h-full">
        {{-- Score --}}
        <div class="flex justify-center w-full">
            <div class="w-full md:px-10">
                <x-score.score
                    :fixture="$fixture"
                    :teams="$teams"
                    :league="$league"
                    :score="$score" />
            </div>
        </div>

        <div class="flex items-center justify-between px-5 gap-x-3 md:hidden">
            {{-- RatedResult --}}
            <x-fixture.result-button
                :$fixture
                :$teams
                :$league
                :$score
                :$lineups
                :$fixtureId />

            @if($canRate)
                {{-- RateAllPlayers --}}
                <x-fixture.rate-all-button
                    :$lineups
                    :$fixtureId />
            @endif
        </div>
        
        <div class="w-full md:hidden">
            <!-- StartingXI -->
            <x-fixture.startXi
                :$fixtureId
                :startXi="$lineups['startXI']">
                <x-slot:substitutes></x-slot:substitutes>
            </x-fixture.startXi>

            <div class="flex justify-center w-full py-3">
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

            <!-- SubStitutes -->
            <div class="grid w-full grid-cols-6 md:gap-10 justify-items-center">
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

        <!-- Responsive(1024px~) -->
        <div class="justify-center hidden md:flex"> 
            <!-- StartingXI -->
            <x-fixture.startXi
                :$fixtureId
                :startXi="$lineups['startXI']">

                <!-- Substitutes -->
                <x-slot:substitutes>
                    <div class="grid content-center h-full gap-10">
                        @foreach(collect($lineups['substitutes'])->flatten(1) as $player)
                            <div class="flex justify-center w-full">
                                <livewire:lineups.player
                                    name="substitutes"
                                    size="w-12 h-12"
                                    :$fixtureId
                                    :$player
                                    :key="$player['id']" />
                            </div>
                        @endforeach
                    </div>
                </x-slot:substitutes>

                <div class="flex items-center w-full justify-evenly gap-x-3">
                    {{-- RatedResult --}}
                    <x-fixture.result-button
                        :$fixture
                        :$teams
                        :$league
                        :$score
                        :$lineups
                        :$fixtureId />

                    @if($canRate)
                        {{-- RateAllPlayers --}}
                        <x-fixture.rate-all-button
                            :$lineups
                            :$fixtureId />
                    @endif

                    {{-- RatedCount --}}
                    <livewire:lineups.rated-count :$fixtureId :$playerCount />

                    {{-- ToggleUserMacine --}}
                    <livewire:lineups.toggle-user-machine />
                </div>
            </x-fixture.startXi>
        </div>
    </div>

    @vite(['resources/css/field.css', 'resources/js/field.js'])
</x-app-layout>