<x-app-layout>
    <div class="p-2" style="height: 90vh;">
        <livewire:message />
     
        <div class="flex items-center w-full h-full">
            <div class="z-10 h-full py-10 ml-10 space-y-5">
                @foreach($lineups['substitutes'] as $player)
                    <livewire:player
                        name="substitutes"
                        :$fixtureId
                        :$player
                        :key="$player['id']" />
                @endforeach
            </div>
            
            <div class="flex items-center justify-center w-full h-full">
                <div class="relative h-full">
                    <div class="relative -top-5" style="height: 88vh;">
                        <x-field.field-svg />
                    </div>
                    
                    <div id="box" class="absolute flex items-end justify-center w-full h-full -top-5">
                        <div class="flex flex-col w-full h-full pt-10">
                            @foreach($lineups['startXI'] as $line => $players)
                                <div id="line-{{ $line + 1 }}"
                                    class="flex items-stretch w-full h-full justify-evenly">
                                    @foreach($players as $player)
                                        <div class="flex justify-center items-center
                                            bg-red-500 border-2 border-green-400
                                            {{ count($players) === 1 ? 'w-full' : 'w-1/'.count($players) }}">
                                            <livewire:player
                                                name="startXI"
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

            <div class="flex flex-col items-center justify-center w-full h-full players">
                <x-score.score
                    :fixture="$fixture"
                    :teams="$teams"
                    :league="$league"
                    :score="$score" />
                
                <livewire:player-detail
                    :$fixtureId
                    :$lineups />
            </div>
        </div>
    </div>

    @vite('resources/js/field.js')
</x-app-layout>