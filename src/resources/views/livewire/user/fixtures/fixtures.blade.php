<div class="flex w-full pb-10">
    <div class="w-full h-full p-2 md:px-8 lg:px-20">
        <div class="relative flex items-center justify-center w-full h-full">
            <div class="flex items-center w-full h-16">
                {{ $this->fixtures->links('components.wire-pagination') }}
            </div>

            {{-- SortTournament --}}
            <div class="absolute flex justify-center w-[60%] z-[99]"
                x-data="{ isOpen: false }">
                <div class="relative flex justify-between items-center w-full bg-gray-500 rounded-lg px-2.5 py-0.5"
                    @click="isOpen = !isOpen"
                    @click.outside="isOpen = false">
                    <p class="px-3 text-base text-gray-300">
                        @foreach($tournaments as $tournament)
                            @if($tournament['value'] === $sort)
                                {{ $tournament['text'] }}
                            @endif
                        @endforeach
                    </p>
                    <x-svg.caret-down class="w-8 h-8 fill-gray-300" />

                    <div x-show="isOpen"
                        x-cloak
                        class="absolute left-0 w-full py-2 mt-3 overflow-hidden bg-gray-500 rounded-lg top-full gap-y-1">
                        @foreach($tournaments as $tournament)
                            <p class="{{ $sort === $tournament['value'] ? 'bg-sky-700' : '' }} px-3 text-md md:text-xl text-gray-300
                                hover:bg-sky-900"
                                wire:click="$set('sort', '{{ $tournament['value'] }}')">
                                {{ $tournament['text'] }}
                            </p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Score --}}
        <div class="w-full mt-2 gap-y-8">
            @foreach($this->fixtures as $fixture)
                <div class="w-full p-1 overflow-hidden border-b border-gray-500">
                    <div class="w-full h-full cursor-pointer"
                        wire:click="toFixture('{{ $fixture->id }}')">
                        <x-fixture.score
                            :fixture="$fixture->fixture"
                            :teams="$fixture->teams"
                            :league="$fixture->league"
                            :score="$fixture->score"
                            :isRate="$fixture->isRate" />
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>