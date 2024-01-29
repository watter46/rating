<div class="flex gap-5 p-5">
    <div class="w-full h-full rounded-xl">
        <div class="relative flex items-center justify-center w-full h-full px-5">
            <div class="flex items-center w-full h-16">
                {{ $this->fixtures->links('components.wire-pagination') }}
            </div>

            <div class="absolute flex items-center justify-center w-1/3 h-full px-5 py-1">
                <select id="tournaments" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 border-transparent focus:border-transparent focus:ring-0 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    wire:model.live="sort">
                    @foreach($tournaments as $tournament)
                        <option class="text-gray-300" value="{{ $tournament['value'] }}">
                            {{ $tournament['text'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="grid mt-2 gap-y-2">
            @foreach($this->fixtures as $fixture)
                <livewire:score
                    :fixtureId="$fixture->id"
                    :fixture="$fixture->fixture"
                    :score="$fixture->score"
                    :winner="$fixture->winner"
                    :isEvaluate="$fixture->isEvaluate"
                    :key="$fixture->id" />
            @endforeach
        </div>
    </div>
</div>