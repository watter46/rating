<div class="flex w-screen pb-10 ">
    <div class="w-full h-full p-2 md:px-8">
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
                            <p class="{{ $sort === $tournament['value'] ? 'bg-sky-700' :'' }} px-3 text-md md:text-xl text-gray-300
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
        <div class="grid mt-2 gap-y-8">
            @foreach($this->fixtures as $fixture)
                <livewire:fixtures.score
                    :fixtureId="$fixture->id"
                    :fixture="$fixture->fixture"
                    :score="$fixture->score"
                    :winner="$fixture->winner"
                    :isRate="$fixture->isRate"
                    :key="$fixture->id" />
            @endforeach
        </div>
    </div>
</div>