<x-admin.app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            DashBoard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="px-2 mx-auto">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-emerald-800 sm:rounded-lg">
                <div x-data="{isOpen: false}" class="p-5">
                    <div class="flex justify-end">
                        <button class="px-5 py-1 border-2 border-gray-200 rounded-lg bg-sky-600"
                            @click="isOpen = true">
                            <p class="font-black text-white">Refresh Latest</p>
                        </button>
                    </div>
            
                    <template x-if="isOpen">
                        <div class="top-0 left-0 z-10 flex items-center justify-center w-full h-full abs olute"
                            style="background: rgba(0, 0, 0, 0.8);">
                            <div class="flex justify-center w-3/6 p-5 bg-gray-500 rounded-lg h-3/6">
                                <form action="/admin/dashboard/refresh" method="POST"
                                    class="relative flex justify-center w-full">
                                    @csrf
                                    <div class="flex flex-col justify-center w-1/2 gap-y-3">
                                        <p class="text-4xl font-black text-center text-gray-200">Refresh Key</p>
                                        <input type="password" name="refreshKey" class="rounded-lg">
                                    </div>
                                    
                                    <div class="absolute bottom-0 right-0 flex justify-end p-3 gap-x-5">
                                        <button type="button" @click="isOpen = false" class="px-5 py-1 bg-gray-600 border-2 border-gray-200 rounded-lg">
                                            <p class="font-black text-white">Cancel</p>
                                        </button>
                    
                                        <button type="submit" class="px-5 py-1 border-2 border-gray-200 rounded-lg bg-sky-600">
                                            <p class="font-black text-white">Refresh</p>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>
                    
                    <div x-show="!isOpen">
                        <div class="grid w-full h-full grid-cols-2 gap-5 p-5">
                            @foreach($fixtures as $fixture)
                                <livewire:admin.fixture
                                    :dataExists="$fixture->dataExists"
                                    :fixtureId="$fixture->id"
                                    :score="$fixture->score"
                                    :key="$fixture->id" />
                            @endforeach
                        </div>
                    
                        <div class="flex justify-center w-full pb-5">
                            {{ $fixtures->links('components.pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.app-layout>