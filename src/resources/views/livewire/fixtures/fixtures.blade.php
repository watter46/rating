<div class="flex pb-10 ">
    <div class="w-full h-full p-2">
        <div class="relative flex items-center justify-center w-full h-full">
            <div class="flex items-center w-full h-16">
                {{ $this->fixtures->links('components.wire-pagination') }}
            </div>

            {{-- SortTournament --}}
            <div class="absolute flex items-center justify-center w-2/3 px-5">
                <select id="tournaments" class="border border-gray-300 rounded-lg block h-full w-full p-2.5 bg-gray-700 border-transparent focus:border-transparent focus:ring-0 dark:border-gray-600 dark:placeholder-gray-400 text-gray-300"
                    wire:model.live="sort">
                    @foreach($tournaments as $tournament)
                        <option value="{{ $tournament['value'] }}">
                            {{ $tournament['text'] }}
                        </option>
                    @endforeach
                </select>
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