<div class="h-full md:ml-5">
    <div class="w-full">
        <x-result.score
            :fixture="$fixture"
            :teams="$teams"
            :score="$score" />
    </div>

    <!-- StartXI(~767px) -->
    <div class="flex items-center justify-center md:hidden">
        <div class="relative w-[95%]">
            <!-- Field -->
            <div class="flex justify-center">
                <x-svg.field-image
                    id="result-field"
                    class="flex-grow initial-state tilted-state field" />

                <!-- Players -->
                <div id="box" class="absolute flex items-end justify-center w-full h-full scale-[0.92] top-5">
                    <div class="flex flex-col w-full h-full">
                        @foreach($lineups['startXI'] as $line => $players)
                            <div id="line-{{ $line + 1 }}"
                                class="flex items-stretch w-full h-full justify-evenly">
                                @foreach($players as $player)
                                    <div class="flex justify-center items-center
                                        {{ count($players) === 1 ? 'w-full' : 'w-1/'.count($players) }}">
                                        <livewire:lineups.rated-player
                                            name="startXI"
                                            size="w-[40px] h-[40px]"
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

    <!-- Substitutes(~767px) -->
    <div class="flex flex-col h-full md:hidden scale-[0.92]">
        <div class="grid content-end w-full h-full grid-cols-6 gap-1 md:gap-5">
            @foreach($lineups['substitutes'] as $index => $substitutes)
                @if($loop->odd)
                    @foreach($substitutes as $key => $player)
                        <div class="flex justify-center w-full col-span-2">
                            <livewire:lineups.rated-player
                                name="substitutes"
                                size="w-[40px] h-[40px]"
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
                                size="w-[40px] h-[40px]"
                                :$fixtureId
                                :$player
                                :key="$player['id']" />
                        </div>
                    @endforeach
                @endif
            @endforeach
        </div>
    </div>

    <!-- Responsive(768px~) -->
    <div class="items-center justify-center hidden w-full h-full bg-red-400 md:flex gap-x-5">
        <div class="relative w-full h-full">
            <div class="flex items-start justify-center w-full h-full">
                {{-- Substitutes --}}
                <div class="grid content-center w-1/2 h-full gap-10 mr-5 right-full">
                    @foreach(collect($lineups['substitutes'])->flatten(1) as $player)
                        <div class="flex justify-center w-full">
                            <livewire:lineups.rated-player
                                name="substitutes"
                                size="w-12 h-12"
                                :$fixtureId
                                :$player
                                :key="$player['id']" />
                        </div>
                    @endforeach
                </div>

                <div class="relative w-full h-full">
                    {{-- Field --}}
                    <div class="flex justify-center w-full h-full">
                        <x-svg.field-image
                            id="fixture-field"
                            class="flex-grow hidden h-[80vh] initial-state field" />

                        {{-- StartXI --}}
                        <div id="box"
                            class="absolute flex justify-center flex-grow w-full h-full field">
                            <div class="absolute bottom-0 flex flex-col w-full h-[90%]">
                                @foreach($lineups['startXI'] as $line => $players)
                                    <div id="line-{{ $line + 1 }}"
                                        class="flex items-stretch w-full h-full justify-evenly">
                                        @foreach($players as $player)
                                            <div class="flex justify-center items-center
                                                {{ count($players) === 1 ? 'w-full' : 'w-1/'.count($players) }}">
                                                <livewire:lineups.rated-player
                                                    name="startXI"
                                                    size="w-12 h-12"
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
    </div>
</div>