<x-app-layout>
    <div class="w-full h-full pb-5">
        <!-- Score -->
        <div class="flex justify-center w-full">
            <div class="w-full md:w-3/4 md:px-10">
                <x-score.score
                    :$fixtureData
                    :$teamsData
                    :$leagueData
                    :$scoreData />
            </div>
        </div>
        
        <div class="flex items-center justify-center w-full h-full">
            <div class="relative flex flex-col justify-center items-center h-full w-full min-w-[300px] max-w-[400px]">
                <!-- Field -->
                <x-svg.field-image
                    id="fixture-field"
                    class="w-[90%] invisible initial-state" />
                
                <!-- StartXI -->
                <div class="w-[90%] absolute top-0 aspect-[74/111]">
                    <div id="box" class="flex items-end justify-center w-full h-full">
                        <div class="flex flex-col w-full h-[90%]">
                            @foreach($lineupsData['startXI'] as $line => $players)
                                <div id="line-{{ $line + 1 }}"
                                    class="flex items-stretch w-full h-full justify-evenly">
                                    @foreach($players as $player)
                                        <div class="flex justify-center items-center
                                            {{ $lineupsData['playerGridCss'] }}">
                                            <livewire:user.fixture.player
                                                name="startXI"
                                                size="w-[40px] h-[40px] md:w-[45px] md:h-[45px]"
                                                :$player
                                                :key="$player['player_info_id']" />
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Substitutes Responsive(~767px) -->
                <div class="w-[90%] top-full right-full mt-5 md:hidden">
                    <div class="grid w-full grid-cols-6 gap-x-10 gap-y-2 justify-items-center">
                        @foreach($lineupsData['mobile_substitutes'] as $substitutes)
                            @if($loop->odd)
                                @foreach($substitutes as $player)
                                    <div class="flex justify-center w-full col-span-2">
                                        <livewire:user.fixture.player
                                            name="substitutes"
                                            size="w-[40px] h-[40px]"
                                            :$player
                                            :key="$player['player_info_id']" />
                                    </div>
                                @endforeach
                            @endif
                            
                            @if($loop->even)
                                @foreach($substitutes as $player)
                                    <div class="col-span-2 flex justify-center w-full
                                        @if($loop->first) col-start-2 @endif">
                                        <livewire:user.fixture.player
                                            name="substitutes"
                                            size="w-[40px] h-[40px]"
                                            :$player
                                            :key="$player['player_info_id']" />
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                </div>
        
                <!-- Substitutes Responsive(768px~) -->
                <div class="absolute hidden h-full mr-5 right-full md:block">
                    <div class="flex items-center h-full">
                        <div class="grid grid-flow-col grid-rows-4 gap-10">
                            @foreach($lineupsData['substitutes'] as $player)
                                <div class="flex justify-center w-full">
                                    <livewire:user.fixture.player
                                        name="substitutes"
                                        size="w-12 h-12"
                                        :$player
                                        :key="$player['player_info_id']" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Options -->
                <div class="flex flex-col w-full px-2 gap-y-5 md:absolute md:left-full"> 
                    <!-- RatedCount -->
                    <livewire:user.fixture.rated-count :$fixtureInfoId />

                    <!-- ToggleUserMacine -->
                    <livewire:user.fixture.toggle-user-machine />

                    <div class="w-fit">
                        <!-- Result -->
                        <x-result.result-button
                            :$fixtureData
                            :$teamsData
                            :$leagueData
                            :$scoreData
                            :$lineupsData
                            :$fixtureInfoId
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/css/field.css', 'resources/js/field.js'])
</x-app-layout>