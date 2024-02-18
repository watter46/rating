<div class="flex items-center justify-center w-full h-full md:justify-evenly">
    <div class="relative flex justify-center items-center w-full min-w-[300px] max-w-[400px]">
        <!-- Field -->
        <x-svg.field-image
            id="fixture-field"
            class="w-[90%]" />
        
        <!-- StartXI -->
        <div class="w-[90%] absolute aspect-[74/111]">
            <div id="box"
                class="flex items-end justify-center w-full h-full">
                <div class="flex flex-col w-full h-[95%]">
                    @foreach($startXi as $line => $players)
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
        <div class="absolute hidden h-full w-fit right-full md:block">
            {{ $substitutes }}
        </div>

        <div class="absolute hidden w-full mt-3 top-full md:block">
            {{ $slot }}
        </div>
    </div>
</div>