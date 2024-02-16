<div class="md:ml-5">
    <div class="flex flex-col justify-center" title="RateAll" wire:click="$toggle('isOpen')">
        <div class="flex justify-center">
            <x-svg.photo-image class="w-8 h-8 cursor-pointer md:w-12 md:h-12 lg:w-8 lg:h-8" />
        </div>
        <p class="text-xs font-black text-center text-gray-400 md:text-lg lg:text-base">Result</p>
    </div>

    @if($isOpen)
        <div class="fixed top-0 left-0 z-[99] flex items-center justify-center w-full h-full px-2 py-3"
            style="background: rgba(31, 41, 55, 0.95);">
            <div class="relative flex flex-col w-full h-full pb-5 bg-gray-900 border border-gray-700 rounded-lg">
                <div class="flex justify-end w-full p-2">
                    <div class="border-gray-400 rounded-full cursor-pointer opacity-30 hover:opacity-100 hover:border"
                        wire:click="$toggle('isOpen')">
                        <x-svg.cross-image class="w-10 h-10 md:w-14 md:h-14 fill-gray-400" />
                    </div>
                </div>

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

                <div class="hidden w-full h-full md:flex">
                    <!-- Substitutes(768px~) -->
                    <div class="content-center hidden h-full pl-2 md:grid gap-y-8 scale-[0.92]">
                        @foreach(collect($lineups['substitutes'])->flatten(1) as $player)
                            <div class="flex justify-center w-full">
                                <livewire:lineups.rated-player
                                    name="substitutes"
                                    size="w-20 h-20"
                                    :$fixtureId
                                    :$player
                                    :key="$player['id']" />
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- StartXI(768px~) -->
                    <div class="flex items-center justify-center w-full">
                        <div class="relative w-[85%]">
                            {{-- Field --}}
                            <div class="flex justify-center">
                                <x-svg.field-image
                                    id="result-field"
                                    class="flex-grow initial-state tilted-state field" />
    
                                {{-- Players --}}
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
                                                            size="w-20 h-20"
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
    @endif
</div>