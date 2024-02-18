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
        
        <div class="flex items-center justify-center w-full h-full md:hidden">
            <div class="relative flex flex-col justify-center items-center h-full w-full min-w-[300px] max-w-[400px]">
                <!-- Field -->
                <x-svg.field-image
                    id="fixture-field"
                    class="w-[90%] invisible initial-state" />
                
                <!-- StartXI -->
                <div class="w-[90%] absolute top-0 aspect-[74/111]">
                    <div id="box"
                        class="flex items-end justify-center w-full h-full">
                        <div class="flex flex-col w-full h-[95%]">
                            @foreach($lineups['startXI'] as $line => $players)
                                <div id="line-{{ $line + 1 }}"
                                    class="flex items-stretch w-full h-full justify-evenly">
                                    @foreach($players as $player)
                                        <div class="flex justify-center items-center
                                            {{ count($players) === 1 ? 'w-full' : 'w-1/'.count($players) }}">
                                            <livewire:lineups.player
                                                name="startXI"
                                                size="w-[40px] h-[40px] md:w-[45px] md:h-[45px]"
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
        
                <!-- Substitutes -->
                <div class="w-[90%] top-full md:right-full mt-5 opacity-60">
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
        
                <div class="flex items-center w-[90%] h-full mt-5 justify-evenly gap-x-3">
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
            </div>
        </div>

        <!-- Responsive(768px~) -->
        <div class="items-center justify-center hidden w-full h-full md:flex md:justify-evenly">
            <div class="relative flex justify-center items-center w-full min-w-[300px] max-w-[400px]">
                <!-- Field -->
                <x-svg.field-image
                    id="fixture-field"
                    class="w-[90%] initial-state" />
                
                <!-- StartXI -->
                <div class="w-[90%] absolute aspect-[74/111]">
                    <div id="box"
                        class="flex items-end justify-center w-full h-full">
                        <div class="flex flex-col w-full h-[95%]">
                            @foreach($lineups['startXI'] as $line => $players)
                                <div id="line-{{ $line + 1 }}"
                                    class="flex items-stretch w-full h-full justify-evenly">
                                    @foreach($players as $player)
                                        <div class="flex justify-center items-center
                                            {{ count($players) === 1 ? 'w-full' : 'w-1/'.count($players) }}">
                                            <livewire:lineups.player
                                                name="startXI"
                                                size="w-[40px] h-[40px] md:w-[45px] md:h-[45px]"
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
        
                <!-- Substitutes -->
                <div class="absolute hidden h-full mr-5 right-full md:block">
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
                </div>
        
                <div class="absolute hidden w-full mt-3 top-full md:block">
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
                </div>
            </div>
        </div>
        
        {{--<div class="justify-center hidden md:flex"> 
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
                    {{~~ RatedResult ~~}}
                    <x-fixture.result-button
                        :$fixture
                        :$teams
                        :$league
                        :$score
                        :$lineups
                        :$fixtureId />

                    @if($canRate)
                        {{~~ RateAllPlayers ~~}}
                        <x-fixture.rate-all-button
                            :$lineups
                            :$fixtureId />
                    @endif

                    {{~~ RatedCount ~~}}
                    <livewire:lineups.rated-count :$fixtureId :$playerCount />

                    {{~~ ToggleUserMacine ~~}}
                    <livewire:lineups.toggle-user-machine />
                </div>
            </x-fixture.startXi>
        </div>--}}
    </div>

    @vite(['resources/css/field.css', 'resources/js/field.js'])
</x-app-layout>