<div class="h-full md:ml-5">
    <div class="w-full">
        <x-result.score
            :$fixtureData
            :$teamsData
            :$scoreData />
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
                                        <livewire:user.result.rated-player
                                            name="startXI"
                                            size="w-[40px] h-[40px] md:w-[45px] md:h-[45px]"
                                            :$fixtureInfoId
                                            :playerData="$player['playerData']"
                                            :player="$player['player']"
                                            :key="$player['playerData']['id']" />
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
                    @foreach($lineupsData['substitutes'] as $substitutes)
                        @if($loop->odd)
                            @foreach($substitutes as $player)
                                <div class="flex justify-center w-full col-span-2">
                                    <livewire:user.result.rated-player
                                        name="substitutes"
                                        size="w-[40px] h-[40px]"
                                        :$fixtureInfoId
                                        :playerData="$player['playerData']"
                                        :player="$player['player']"
                                        :key="$player['playerData']['id']" />
                                </div>
                            @endforeach
                        @endif

                        @if($loop->even)
                            @foreach($substitutes as $player)
                                <div class="col-span-2 flex justify-center w-full
                                    @if($loop->first) col-start-2 @endif">
                                    <livewire:user.result.rated-player
                                        name="substitutes"
                                        size="w-[40px] h-[40px]"
                                        :$fixtureInfoId
                                        :playerData="$player['playerData']"
                                        :player="$player['player']"
                                        :key="$player['playerData']['id']" />
                                </div>
                            @endforeach
                        @endif
                    @endforeach
                </div>
            </div>
    
            <!-- Substitutes Responsive(768px~) -->
            <div class="absolute hidden h-full mr-5 right-full md:block">
                <div class="grid content-center h-full gap-10">
                    @foreach(collect($lineupsData['substitutes'])->flatten(1) as $player)
                        <div class="flex justify-center w-full">
                            <livewire:user.result.rated-player
                                name="substitutes"
                                size="w-12 h-12"
                                :$fixtureInfoId
                                :playerData="$player['playerData']"
                                :player="$player['player']"
                                :key="$player['playerData']['id']" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/css/field.css', 'resources/js/field.js'])
</div>