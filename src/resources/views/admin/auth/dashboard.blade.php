<x-admin.app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            DashBoard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-emerald-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-white">表示されている試合を一覧で表示する</h1>
                    <h1 class="text-white">最新の試合のデータを取得する</h1>
                </div>
            </div>
        </div>
    </div>

    <form action="/admin/dashboard/update" method="GET">
        <button class="px-5 py-1 bg-green-600 border-2 border-gray-200 rounded-lg">
            <p class="font-black text-white">Update</p>
        </button>
    </form>

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
</x-admin.app-layout>