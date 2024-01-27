<div class="pt-12">
    <div class="px-2 mx-auto">
        <div class="overflow-hidden bg-white shadow-sm dark:bg-emerald-800 sm:rounded-lg">
            <div x-data="{isOpen: false}" class="p-5"
                @close-fixtures-modal.window="isOpen = false">
                <div class="flex justify-end">
                    <button class="px-5 py-1 border-2 border-gray-200 rounded-lg bg-sky-600"
                        @click="isOpen = true">
                        <p class="font-black text-white">Refresh Squads</p>
                    </button>
                </div>
        
                <template x-if="isOpen">
                    <div class="absolute top-0 left-0 z-10 flex items-center justify-center w-full h-full"
                        style="background: rgba(0, 0, 0, 0.8);">
                        <div class="flex justify-center w-3/6 p-5 bg-gray-500 rounded-lg h-3/6">
                            <div class="relative flex justify-center w-full">                                
                                <div class="flex flex-col justify-center w-1/2 gap-y-3">
                                    <p class="text-4xl font-black text-center text-gray-200">Refresh Key</p>
                                    <input type="password" class="rounded-lg" wire:model="refreshKey">
                                </div>
                                
                                <div class="absolute bottom-0 right-0 flex justify-end p-3 gap-x-5">
                                    <button type="button" @click="isOpen = false" class="px-5 py-1 bg-gray-600 border-2 border-gray-200 rounded-lg">
                                        <p class="font-black text-white">Cancel</p>
                                    </button>
                
                                    <button class="px-5 py-1 border-2 border-gray-200 rounded-lg bg-sky-600"
                                        wire:click="refreshSquads"
                                        wire:loading.class="opacity-50"
                                        wire:loading.attr="disabled">

                                        <p class="font-black text-white" wire:loading.class.add="hidden">Refresh</p>
                                        <p class="hidden font-black text-white" wire:loading.class.remove="hidden">Saving...</p>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="!isOpen">
                    <div class="grid w-full h-full gap-3 mt-3">
                        @foreach($this->players as $player)
                            <div class="flex items-center text-center text-gray-300 border-b border-gray-500 gap-x-5">
                                <img src="{{ $player->img['data'] }}" class="rounded-full w-14 h-14">
                                <p class="w-5 text-xl">{{ $player->number ?? '-' }}</p>
                                <p class="text-2xl">{{ $player->name }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>