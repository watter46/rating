<x-app-layout>
    <div class="grid w-full h-full grid-cols-2 gap-5 p-5">
        @foreach($fixtures as $fixture)
            <livewire:score
                :fixtureId="$fixture->id"
                :score="$fixture->score"
                :key="$fixture->id" />
        @endforeach
    </div>

    <div class="flex justify-center w-full pb-5">
        {{ $fixtures->links('components.pagination') }}
    </div>
</x-app-layout>